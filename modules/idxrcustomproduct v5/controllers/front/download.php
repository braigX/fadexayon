<?php
if (isset($_GET['file'])) {
    // Decode the URL to get the actual file path
    $file_url = urldecode($_GET['file']);

    // Ensure the URL is valid and points to an existing file
    $headers = get_headers($file_url, 1);
    
    // Check if the file exists on the remote server
    if ($headers && strpos($headers[0], '200') !== false) {
        // Extract the file name from the URL
        $file_name = basename($file_url);

        // Set appropriate headers to force the file download
        header('Content-Type: application/octet-stream');  // Generic download MIME type
        header("Content-Transfer-Encoding: binary");       // Indicate binary file transfer
        header("Content-disposition: attachment; filename=\"$file_name\"");  // Prompt the browser to download the file

        // Now, read and output the remote file content to the browser
        // Use curl to fetch the file content from the remote server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects (if any)
        curl_setopt($ch, CURLOPT_HEADER, 0); // Do not include headers in the output

        // Fetch the content of the remote file
        $file_content = curl_exec($ch);
        curl_close($ch);

        // Check if we got valid content
        if ($file_content === false) {
            echo "Error: Unable to fetch the file.";
            exit;
        }

        // Output the file content to the browser
        echo $file_content;
        exit;
    } else {
        echo 'Le fichier est introuvable ou inaccessible. Veuillez vérifier l\'URL et réessayer.';
    }
} else {
    echo 'Aucun fichier spécifié. Veuillez fournir un fichier valide pour le téléchargement.';
}
?>
