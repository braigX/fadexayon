<?php
echo "<h3>Test Redis Connection</h3>";

// Test 1 : avec 127.0.0.1
try {
    $redis = new Redis();
    $connected = $redis->connect('127.0.0.1', 6379, 2.5);
    if ($connected) {
        echo "✅ Connexion réussie à 127.0.0.1:6379<br>";
        $redis->set('test_key', 'Hello Redis!');
        echo "✅ Test écriture/réussie : " . $redis->get('test_key') . "<br>";
    } else {
        echo "❌ Échec connexion à 127.0.0.1:6379<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur avec 127.0.0.1: " . $e->getMessage() . "<br>";
}

// Test 2 : avec localhost
try {
    $redis2 = new Redis();
    $connected2 = $redis2->connect('localhost', 6379, 2.5);
    if ($connected2) {
        echo "✅ Connexion réussie à localhost:6379<br>";
    } else {
        echo "❌ Échec connexion à localhost:6379<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur avec localhost: " . $e->getMessage() . "<br>";
}

// Test 3 : vérification extension
echo "<br>Extension Redis chargée : " . (extension_loaded('redis') ? '✅ OUI' : '❌ NON');
?>