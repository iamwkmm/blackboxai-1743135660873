<?php
require_once __DIR__ . '/../config/config.php';

// Verificar si el usuario está autorizado
if (!isset($_SERVER['PHP_AUTH_USER']) || 
    !isset($_SERVER['PHP_AUTH_PW']) || 
    $_SERVER['PHP_AUTH_USER'] !== 'admin' || 
    $_SERVER['PHP_AUTH_PW'] !== 'your_secure_password') {
    header('WWW-Authenticate: Basic realm="Acceso restringido"');
    header('HTTP/1.0 401 Unauthorized');
    exit('Acceso no autorizado');
}

try {
    // Iniciar documento XML
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->setIndent(true);
    $xml->setIndentString('    ');
    $xml->startDocument('1.0', 'UTF-8');

    // Iniciar elemento urlset con namespaces
    $xml->startElement('urlset');
    $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xml->writeAttribute('xmlns:news', 'http://www.google.com/schemas/sitemap-news/0.9');
    $xml->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
    $xml->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

    // Obtener la conexión a la base de datos
    $db = Database::getInstance()->getConnection();

    // Agregar página principal
    addUrl($xml, BASE_URL . '/', 'daily', '1.0', date('Y-m-d'));

    // Agregar sección de noticias
    addUrl($xml, BASE_URL . '/noticias', 'hourly', '0.9', date('Y-m-d'));

    // Obtener y agregar categorías
    $stmt = $db->query("SELECT slug, fecha_creacion FROM web_categoria");
    while ($categoria = $stmt->fetch()) {
        addUrl(
            $xml, 
            BASE_URL . '/categoria/' . $categoria['slug'],
            'daily',
            '0.8',
            date('Y-m-d', strtotime($categoria['fecha_creacion']))
        );
    }

    // Obtener y agregar noticias
    $stmt = $db->query(
        "SELECT n.LINK, n.FECHA, n.TITULO, n.IMG_PORTADA, c.nombre as categoria_nombre 
         FROM web_noticia n 
         JOIN web_categoria c ON n.CATEGORIA = c.id 
         WHERE n.VISIBLE = 1 
         ORDER BY n.FECHA DESC 
         LIMIT 1000" // Limitar a las 1000 noticias más recientes
    );

    while ($noticia = $stmt->fetch()) {
        $xml->startElement('url');
        $xml->writeElement('loc', BASE_URL . '/noticia/' . $noticia['LINK']);
        $xml->writeElement('lastmod', date('Y-m-d', strtotime($noticia['FECHA'])));
        $xml->writeElement('changefreq', 'never');
        $xml->writeElement('priority', '0.6');

        // Agregar metadatos de noticia
        $xml->startElement('news:news');
        $xml->startElement('news:publication');
        $xml->writeElement('news:name', 'Octava Digital');
        $xml->writeElement('news:language', 'es');
        $xml->endElement(); // news:publication
        $xml->writeElement('news:publication_date', date('c', strtotime($noticia['FECHA'])));
        $xml->writeElement('news:title', htmlspecialchars($noticia['TITULO']));
        $xml->endElement(); // news:news

        // Agregar imagen si existe
        if ($noticia['IMG_PORTADA']) {
            $xml->startElement('image:image');
            $xml->writeElement('image:loc', BASE_URL . '/uploads/news/' . $noticia['IMG_PORTADA']);
            $xml->writeElement('image:title', htmlspecialchars($noticia['TITULO']));
            $xml->endElement(); // image:image
        }

        $xml->endElement(); // url
    }

    // Agregar páginas estáticas
    $static_pages = [
        '/acerca-de' => ['monthly', '0.5'],
        '/contacto' => ['monthly', '0.5'],
        '/politica-privacidad' => ['yearly', '0.3'],
        '/terminos-condiciones' => ['yearly', '0.3']
    ];

    foreach ($static_pages as $page => $config) {
        addUrl($xml, BASE_URL . $page, $config[0], $config[1], date('Y-m-d'));
    }

    // Cerrar elemento urlset
    $xml->endElement();

    // Guardar el archivo
    $sitemap_content = $xml->outputMemory();
    $sitemap_path = __DIR__ . '/sitemap.xml';
    
    if (file_put_contents($sitemap_path, $sitemap_content)) {
        // Notificar a los motores de búsqueda
        $search_engines = [
            'Google' => 'https://www.google.com/ping?sitemap=' . urlencode(BASE_URL . '/sitemap.xml'),
            'Bing' => 'https://www.bing.com/ping?sitemap=' . urlencode(BASE_URL . '/sitemap.xml')
        ];

        foreach ($search_engines as $name => $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        // Enviar respuesta exitosa
        header('Content-Type: application/xml; charset=utf-8');
        echo $sitemap_content;
        
        // Log de la actualización
        error_log("Sitemap actualizado exitosamente: " . date('Y-m-d H:i:s'));
    } else {
        throw new Exception("No se pudo guardar el archivo sitemap.xml");
    }

} catch (Exception $e) {
    // Log del error
    error_log("Error generando sitemap: " . $e->getMessage());
    
    // Enviar respuesta de error
    header('HTTP/1.1 500 Internal Server Error');
    echo "Error generando el sitemap";
}

// Función auxiliar para agregar URLs
function addUrl($xml, $loc, $changefreq, $priority, $lastmod) {
    $xml->startElement('url');
    $xml->writeElement('loc', $loc);
    $xml->writeElement('changefreq', $changefreq);
    $xml->writeElement('priority', $priority);
    $xml->writeElement('lastmod', $lastmod);
    $xml->endElement();
}

// Función para limpiar y validar URLs
function cleanUrl($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}