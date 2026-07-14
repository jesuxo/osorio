<?php
// app/Services/ChatbotService.php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Ia;
use App\Models\Saprod;
use App\Models\NewSaexis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private string $geminiApiKey;
    private string $modelName;
    private array $validModels = [
        'models/gemini-2.5-flash'
    ];

    // Cache de productos en memoria
    private array $productsCache = [];
    private array $productKeywords = [];

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->modelName = $this->getValidModelName(env('MODEL_NAME', 'models/gemini-2.5-flash'));

        // Cargar productos en memoria al instanciar
        $this->loadProductsIntoMemory();
    }

    private function getValidModelName(string $requestedModel): string
    {
        if (in_array($requestedModel, $this->validModels)) {
            return $requestedModel;
        }
        return 'models/gemini-2.5-flash';
    }

    /**
     * Cargar todos los productos con existencia en memoria
     */
    private function loadProductsIntoMemory(): void
    {
        try {
            $comercial = 1;

            $products = Saprod::where('comercial', $comercial)
                ->where('activo', 1)
                ->get();

            $this->productsCache = [];
            $this->productKeywords = [];

            foreach ($products as $product) {
                $existencias = NewSaexis::where('codprod', $product->codprod)
                    ->where('existen', '>', 0)
                    ->sum('existen');

                if ($existencias > 0) {
                    $productData = [
                        'codprod' => $product->codprod,
                        'descrip' => $product->descrip,
                        'descrip2' => $product->descrip2 ?? '',
                        'marca' => $product->marca ?? '',
                        'refere' => $product->refere ?? '',
                        'precio' => (float)($product->costod3 ?? $product->preciod ?? 0),
                        'preciod' => (float)($product->preciod ?? 0),
                        'preciod2' => (float)($product->preciod2 ?? 0),
                        'costod' => (float)($product->costod ?? 0),
                        'costod2' => (float)($product->costod2 ?? 0),
                        'costod3' => (float)($product->costod3 ?? 0),
                        'stock' => (int)$existencias,
                        'unidad' => $product->unidad ?? 'unidad',
                        'categoria' => $this->getProductCategory($product->codinst)
                    ];

                    $this->productsCache[] = $productData;

                    // Extraer palabras clave para búsqueda rápida
                    $keywords = array_merge(
                        explode(' ', strtolower($product->descrip)),
                        explode(' ', strtolower($product->descrip2 ?? '')),
                        explode(' ', strtolower($product->marca ?? '')),
                        [$product->codprod]
                    );

                    foreach ($keywords as $keyword) {
                        if (strlen($keyword) > 2) {
                            if (!isset($this->productKeywords[$keyword])) {
                                $this->productKeywords[$keyword] = [];
                            }
                            $this->productKeywords[$keyword][] = count($this->productsCache) - 1;
                        }
                    }
                }
            }

            Log::info('Productos cargados en memoria: ' . count($this->productsCache));

        } catch (\Exception $e) {
            Log::error('Error cargando productos en memoria: ' . $e->getMessage());
        }
    }

    /**
     * Obtener categoría del producto
     */
    private function getProductCategory($codinst): string
    {
        try {
            $categoria = DB::table('sainsta')
                ->where('codinst', $codinst)
                ->value('descrip');
            return $categoria ?? 'General';
        } catch (\Exception $e) {
            return 'General';
        }
    }

    /**
     * Procesa el mensaje del usuario y retorna una respuesta
     */
    public function processMessage(string $message, $conversation_id): array
    {
        try {
            // PASO 1: Corregir ortografía del mensaje
            $correctedMessage = $this->correctSpelling($message);

            // PASO 2: Identificar productos en el mensaje
            $mentionedProducts = $this->identifyProductsInMessage($correctedMessage);

            // PASO 3: Construir prompt con todos los productos en memoria
            $prompt = $this->buildPromptWithProducts($correctedMessage, $mentionedProducts);

            // PASO 4: Llamar a Gemini para obtener respuesta
            $aiResponse = $this->callGeminiApi($prompt);

            // PASO 5: Procesar la respuesta y extraer productos
            $result = $this->processAIResponse($aiResponse, $mentionedProducts);

            // Guardar mensaje del asistente
            ChatMessage::create([
                'chat_conversation_id' => $conversation_id,
                'sender' => 'assistant',
                'message' => $result['reply'],
                'metadata' => [
                    'timestamp' => now(),
                    'corrected_message' => $correctedMessage,
                    'products_found' => $result['products'] ?? []
                ]
            ]);

            return [
                'success' => true,
                'reply' => $result['reply'],
                'products' => $result['products'] ?? null,
                'mensaje' => $result['mensaje'] ?? null,
                'total_products' => count($result['products'] ?? []),
                'corrected_message' => $correctedMessage // Opcional: mostrar corrección al usuario
            ];

        } catch (\Exception $exception) {
            Log::error('Error en processMessage: ' . $exception->getMessage());

            $errorMessage = 'Lo siento, tuve un problema técnico. ¿Podrías intentarlo de nuevo?';

            ChatMessage::create([
                'chat_conversation_id' => $conversation_id,
                'sender' => 'assistant',
                'message' => $errorMessage,
                'metadata' => [
                    'timestamp' => now(),
                    'error' => $exception->getMessage()
                ]
            ]);

            return [
                'success' => false,
                'reply' => $errorMessage,
                'error' => $exception->getMessage()
            ];
        }
    }

    /**
     * Corregir ortografía del mensaje usando Gemini
     */
    private function correctSpelling(string $message): string
    {
        try {
            $prompt = "Corrige la ortografía y gramática del siguiente mensaje sin cambiar el significado ni el tono.
                       Solo corrige errores ortográficos y gramaticales, no agregues ni elimines información.
                       Devuelve SOLO el mensaje corregido, sin explicaciones adicionales.

                       Mensaje original: \"{$message}\"

                       Mensaje corregido:";

            $corrected = $this->callGeminiApi($prompt);

            // Si la corrección está vacía o es muy corta, devolver el original
            if (strlen($corrected) < 5) {
                return $message;
            }

            return $corrected;

        } catch (\Exception $e) {
            Log::warning('Error corrigiendo ortografía: ' . $e->getMessage());
            return $message; // Devolver mensaje original si falla
        }
    }

    /**
     * Identificar productos en el mensaje usando la memoria
     */
    private function identifyProductsInMessage(string $message): array
    {
        $foundProducts = [];
        $messageLower = strtolower($message);

        // Buscar por palabras clave en el mensaje
        $messageWords = explode(' ', $messageLower);
        $messageWords = array_filter($messageWords, function($word) {
            return strlen($word) > 2;
        });

        $productIndices = [];

        foreach ($messageWords as $word) {
            if (isset($this->productKeywords[$word])) {
                $productIndices = array_merge($productIndices, $this->productKeywords[$word]);
            }
        }

        // Buscar coincidencias exactas en descripciones
        foreach ($this->productsCache as $index => $product) {
            $descripLower = strtolower($product['descrip']);

            // Verificar si alguna palabra clave del mensaje está en la descripción
            foreach ($messageWords as $word) {
                if (strpos($descripLower, $word) !== false) {
                    $productIndices[] = $index;
                    break;
                }
            }
        }

        // Eliminar duplicados y obtener productos únicos
        $productIndices = array_unique($productIndices);

        foreach ($productIndices as $index) {
            if (isset($this->productsCache[$index])) {
                $foundProducts[] = $this->productsCache[$index];
            }
        }

        return $foundProducts;
    }

    /**
     * Construir prompt con todos los productos en memoria
     */
    private function buildPromptWithProducts(string $message, array $mentionedProducts): string
    {
        $prompt = "Eres un vendedor amable y cordial del Grupo Osorio llamado Oso. ";
        $prompt .= "Respondes SIEMPRE en español, a menos que el cliente escriba en inglés.\n\n";

        $prompt .= "CATÁLOGO DE PRODUCTOS DISPONIBLES (con existencia en inventario):\n";
        $prompt .= "==========================================================\n\n";

        if (empty($this->productsCache)) {
            $prompt .= "No hay productos disponibles en este momento.\n\n";
        } else {
            // Mostrar todos los productos disponibles
            foreach ($this->productsCache as $index => $product) {
                $prompt .= ($index + 1) . ". ";
                $prompt .= "Código: {$product['codprod']} | ";
                $prompt .= "Producto: {$product['descrip']}";

                if (!empty($product['marca'])) {
                    $prompt .= " (Marca: {$product['marca']})";
                }

                if (!empty($product['descrip2'])) {
                    $prompt .= " - {$product['descrip2']}";
                }

                $prompt .= " | Precio: $" . number_format($product['precio'], 2);
                $prompt .= " | Stock: {$product['stock']} {$product['unidad']}";

                if (!empty($product['categoria']) && $product['categoria'] !== 'General') {
                    $prompt .= " | Categoría: {$product['categoria']}";
                }

                $prompt .= "\n";
            }
        }

        $prompt .= "\n==========================================================\n\n";

        // Información de productos mencionados
        if (!empty($mentionedProducts)) {
            $prompt .= "PRODUCTOS QUE PODRÍAN ESTAR RELACIONADOS CON LA CONSULTA DEL CLIENTE:\n";
            $prompt .= "----------------------------------------------------------\n";
            foreach ($mentionedProducts as $product) {
                $prompt .= "- {$product['descrip']} (Código: {$product['codprod']}, Stock: {$product['stock']}, Precio: $" . number_format($product['precio'], 2) . ")\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "REGLAS IMPORTANTES:\n";
        $prompt .= "1. Analiza el mensaje del cliente y busca en el catálogo si hay productos relacionados.\n";
        $prompt .= "2. Si el cliente menciona productos específicos, verifica si están en el catálogo.\n";
        $prompt .= "3. Si encuentras productos relacionados, menciónalos con su código, descripción y precio.\n";
        $prompt .= "4. Si el producto NO está en el catálogo, indícalo amablemente y ofrécele alternativas similares.\n";
        $prompt .= "5. NO inventes productos que no estén en el catálogo.\n";
        $prompt .= "6. Responde de manera natural y amable.\n";
        $prompt .= "7. Si hay varios productos relacionados, enuméralos en una lista.\n";
        $prompt .= "8. Pregunta si necesita más información o quiere hacer una cotización.\n";
        $prompt .= "9. No uses frases como 'muy amablemente' o 'saludos cordiales'.\n";
        $prompt .= "10. Si el mensaje fue corregido ortográficamente, no menciones la corrección.\n\n";

        $prompt .= "MENSAJE DEL CLIENTE:\n";
        $prompt .= "{$message}\n\n";

        $prompt .= "RESPUESTA (solo el mensaje para el cliente, sin etiquetas ni marcadores):\n";

        return $prompt;
    }

    /**
     * Procesar respuesta de la IA y extraer productos
     */
    private function processAIResponse(string $aiResponse, array $mentionedProducts): array
    {
        $result = [
            'reply' => $aiResponse,
            'products' => [],
            'mensaje' => null
        ];

        // Verificar si la IA mencionó productos
        $mentionedInResponse = [];

        foreach ($this->productsCache as $product) {
            $codprod = $product['codprod'];
            $descrip = $product['descrip'];

            if (strpos($aiResponse, $codprod) !== false ||
                strpos($aiResponse, $descrip) !== false ||
                strpos(strtolower($aiResponse), strtolower($descrip)) !== false) {
                $mentionedInResponse[] = $product;
            }
        }

        // Si la IA mencionó productos en su respuesta, usarlos
        if (!empty($mentionedInResponse)) {
            $result['products'] = $mentionedInResponse;

            // Intentar extraer el mensaje introductorio
            $lines = explode("\n", $aiResponse);
            $introLines = [];
            $foundProducts = false;

            foreach ($lines as $line) {
                if (preg_match('/^\d+\./', trim($line)) || preg_match('/^-/', trim($line))) {
                    $foundProducts = true;
                } else if (!$foundProducts && !empty(trim($line))) {
                    $introLines[] = $line;
                }
            }

            if (!empty($introLines)) {
                $result['mensaje'] = implode("\n", $introLines);
            } else {
                $total = count($mentionedInResponse);
                $result['mensaje'] = $total === 1
                    ? "Encontramos el siguiente producto que podría interesarte:"
                    : "Encontramos {$total} productos disponibles:";
            }
        }
        // Si no mencionó productos pero teníamos productos mencionados por el cliente
        else if (!empty($mentionedProducts)) {
            $result['products'] = $mentionedProducts;
            $total = count($mentionedProducts);
            $result['mensaje'] = $total === 1
                ? "Encontramos el siguiente producto que podría interesarte:"
                : "Encontramos {$total} productos disponibles:";
        }

        return $result;
    }

    /**
     * Obtener todos los productos en memoria (para debugging)
     */
    public function getProductsInMemory(): array
    {
        return $this->productsCache;
    }

    /**
     * Recargar productos en memoria
     */
    public function refreshProductsCache(): void
    {
        $this->loadProductsIntoMemory();
    }

    public function getRelevantKnowledge(string $userQuery, int $limit = 5): array
    {
        $knowledge = Ia::active()
            ->ordered()
            ->get();
        return $knowledge->toArray();
    }

    public function formatForAi(array $knowledgeItems): string
    {
        $context = "Información de la empresa:\n\n";
        foreach($knowledgeItems as $item) {
            $context .= "TÍTULO: {$item['title']}\n";
            if(!empty($item['category'])) {
                $context .= "CATEGORÍA: {$item['category']}\n";
            }
            $context .= "CONTENIDO: {$item['text']}\n";
            $context .= str_repeat('-', 50) . "\n\n";
        }
        return $context;
    }

    /**
     * Llama a la API de Gemini
     */
    private function callGeminiApi(string $prompt): string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/{$this->modelName}:generateContent?key={$this->geminiApiKey}";

            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API error: ' . $response->body());
                return 'Lo siento, hubo un error al procesar tu consulta. Por favor, intenta de nuevo.';
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ??
                $data['candidates'][0]['text'] ??
                null;

            if (!$text) {
                Log::warning('Respuesta inesperada de Gemini', ['data' => $data]);
                return 'No pude generar una respuesta adecuada. ¿Podrías reformular tu pregunta?';
            }

            return trim($text);

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
            return 'Lo siento, hay problemas de conexión. Por favor intenta más tarde.';
        }
    }

    public function health(): array
    {
        $status = [
            'gemini_api' => 'unknown',
            'database' => 'unknown',
            'products_in_memory' => count($this->productsCache)
        ];

        try {
            $testResponse = $this->callGeminiApi('Responde solo "ok"');
            $status['gemini_api'] = !empty($testResponse) ? 'ok' : 'error';
        } catch (\Exception $e) {
            $status['gemini_api'] = 'error';
        }

        try {
            DB::select('SELECT 1');
            $status['database'] = 'ok';
        } catch (\Exception $e) {
            $status['database'] = 'error';
        }

        return $status;
    }
}
