<?php
/**
 * Blizzard API Integration Module
 * 
 * Módulo para integración con la Blizzard Battle.net API
 * usando cURL nativo de PHP para consultar datos de personajes WoW
 * 
 * @author WoW Test Manager
 * @version 1.0
 */

// Configuración de la API
if (!defined('BLIZZARD_REGION')) {
    define('BLIZZARD_REGION', 'eu');
}
if (!defined('BLIZZARD_LOCALE')) {
    define('BLIZZARD_LOCALE', 'es_ES');
}
if (!defined('BLIZZARD_OAUTH_URL')) {
    define('BLIZZARD_OAUTH_URL', 'https://oauth.battle.net/token');
}
if (!defined('BLIZZARD_API_URL')) {
    define('BLIZZARD_API_URL', 'https://eu.api.blizzard.com');
}

class BlizzardAPI
{
    private $clientId;
    private $clientSecret;
    private $accessToken = null;
    private $tokenExpiry = 0;
    private $cacheDir;

    /**
     * Constructor
     * 
     * @param string $clientId Client ID de Blizzard Developer Portal
     * @param string $clientSecret Client Secret de Blizzard Developer Portal
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cacheDir = __DIR__ . '/../cache/blizzard/';

        // Crear directorio de caché si no existe
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Autenticación OAuth2 con Blizzard
     * Obtiene un access token usando el flujo de credenciales de cliente
     * 
     * @return bool True si la autenticación fue exitosa
     */
    public function authenticate()
    {
        // Verificar si ya tenemos un token válido
        if ($this->accessToken && time() < $this->tokenExpiry) {
            return true;
        }

        $ch = curl_init(BLIZZARD_OAUTH_URL);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);

            if (isset($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                $this->tokenExpiry = time() + ($data['expires_in'] ?? 86400) - 300; // 5 min buffer
                return true;
            }
        }

        error_log("Blizzard API Auth Error (HTTP $httpCode): $error");
        return false;
    }

    /**
     * Obtener perfil de personaje
     * 
     * @param string $realm Nombre del reino (ej: "ragnaros")
     * @param string $name Nombre del personaje
     * @return array|null Datos del personaje o null si hay error
     */
    public function getCharacterProfile($realm, $name)
    {
        if (!$this->authenticate()) {
            return null;
        }

        $cacheKey = "profile_{$realm}_{$name}";

        // Intentar obtener de caché (TTL: 1 hora)
        $cached = $this->getCachedData($cacheKey, 3600);
        if ($cached !== null) {
            return $cached;
        }

        $realm = strtolower(str_replace(' ', '-', $realm));
        $name = strtolower($name);

        $url = BLIZZARD_API_URL . "/profile/wow/character/{$realm}/{$name}";
        $url .= "?namespace=profile-" . BLIZZARD_REGION . "&locale=" . BLIZZARD_LOCALE;

        $data = $this->makeRequest($url);

        if ($data) {
            $this->setCachedData($cacheKey, $data);
        }

        return $data;
    }

    /**
     * Obtener equipamiento de personaje
     * 
     * @param string $realm Nombre del reino
     * @param string $name Nombre del personaje
     * @return array|null Datos del equipamiento o null si hay error
     */
    public function getCharacterEquipment($realm, $name)
    {
        if (!$this->authenticate()) {
            return null;
        }

        $cacheKey = "equipment_{$realm}_{$name}";

        // Intentar obtener de caché (TTL: 30 minutos)
        $cached = $this->getCachedData($cacheKey, 1800);
        if ($cached !== null) {
            return $cached;
        }

        $realm = strtolower(str_replace(' ', '-', $realm));
        $name = strtolower($name);

        $url = BLIZZARD_API_URL . "/profile/wow/character/{$realm}/{$name}/equipment";
        $url .= "?namespace=profile-" . BLIZZARD_REGION . "&locale=" . BLIZZARD_LOCALE;

        $data = $this->makeRequest($url);

        if ($data) {
            $this->setCachedData($cacheKey, $data);
        }

        return $data;
    }

    /**
     * Obtener media de personaje (avatar, render, etc.)
     * 
     * @param string $realm Nombre del reino
     * @param string $name Nombre del personaje
     * @return array|null URLs de las imágenes o null si hay error
     */
    public function getCharacterMedia($realm, $name)
    {
        if (!$this->authenticate()) {
            return null;
        }

        $cacheKey = "media_{$realm}_{$name}";

        // Caché de 24 horas (las imágenes cambian poco)
        $cached = $this->getCachedData($cacheKey, 86400);
        if ($cached !== null) {
            return $cached;
        }

        $realm = strtolower(str_replace(' ', '-', $realm));
        $name = strtolower($name);

        $url = BLIZZARD_API_URL . "/profile/wow/character/{$realm}/{$name}/character-media";
        $url .= "?namespace=profile-" . BLIZZARD_REGION . "&locale=" . BLIZZARD_LOCALE;

        $data = $this->makeRequest($url);

        if ($data) {
            $this->setCachedData($cacheKey, $data);
        }

        return $data;
    }

    /**
     * Realizar petición HTTP a la API
     * 
     * @param string $url URL completa del endpoint
     * @return array|null Datos decodificados o null si hay error
     */
    private function makeRequest($url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Manejo de errores HTTP
        switch ($httpCode) {
            case 200:
                return json_decode($response, true);

            case 401:
                error_log("Blizzard API: Token inválido o expirado");
                $this->accessToken = null; // Forzar re-autenticación
                return null;

            case 404:
                error_log("Blizzard API: Personaje no encontrado - $url");
                return null;

            case 429:
                error_log("Blizzard API: Rate limit excedido");
                return null;

            default:
                error_log("Blizzard API Error (HTTP $httpCode): $error - $url");
                return null;
        }
    }

    /**
     * Obtener datos de caché
     * 
     * @param string $key Clave de caché
     * @param int $ttl Tiempo de vida en segundos
     * @return mixed|null Datos cacheados o null si no existe/expiró
     */
    private function getCachedData($key, $ttl = 3600)
    {
        $cacheFile = $this->cacheDir . md5($key) . '.cache';

        if (file_exists($cacheFile)) {
            $age = time() - filemtime($cacheFile);

            if ($age < $ttl) {
                $content = file_get_contents($cacheFile);
                return json_decode($content, true);
            }
        }

        return null;
    }

    /**
     * Guardar datos en caché
     * 
     * @param string $key Clave de caché
     * @param mixed $data Datos a cachear
     * @return bool True si se guardó correctamente
     */
    private function setCachedData($key, $data)
    {
        $cacheFile = $this->cacheDir . md5($key) . '.cache';
        $content = json_encode($data);

        return file_put_contents($cacheFile, $content) !== false;
    }

    /**
     * Limpiar caché antigua (más de 7 días)
     * 
     * @return int Número de archivos eliminados
     */
    public function cleanOldCache()
    {
        $count = 0;
        $maxAge = 7 * 24 * 3600; // 7 días

        foreach (glob($this->cacheDir . '*.cache') as $file) {
            if (time() - filemtime($file) > $maxAge) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Extraer información útil del perfil
     * 
     * @param array $profile Datos del perfil de Blizzard API
     * @return array|null Datos simplificados o null si no hay perfil
     */
    public static function extractProfileData($profile)
    {
        if (!$profile) {
            return null;
        }

        return [
            'name' => $profile['name'] ?? 'Desconocido',
            'level' => $profile['level'] ?? 0,
            'class' => $profile['character_class']['name'] ?? 'Desconocida',
            'class_id' => $profile['character_class']['id'] ?? 0,
            'race' => $profile['race']['name'] ?? 'Desconocida',
            'faction' => $profile['faction']['name'] ?? 'Neutral',
            'realm' => $profile['realm']['name'] ?? 'Desconocido',
            'gender' => $profile['gender']['name'] ?? 'Desconocido',
            'achievement_points' => $profile['achievement_points'] ?? 0,
            'average_item_level' => $profile['average_item_level'] ?? 0,
            'equipped_item_level' => $profile['equipped_item_level'] ?? 0
        ];
    }
}
?>