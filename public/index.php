<?php
/**
 * Public entry point for 5250-to-web-ui.
 * Start with: php -S localhost:8080 -t public
 */

// Display a simple landing page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>5250 to Web UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4">5250 to Web UI</h1>
            <p class="lead">Modernize your IBM i green-screen applications</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">🔍 Analyze</h5>
                        <p class="card-text">Parse DDS display files to extract screen structure, fields, validation rules, and navigation flows.</p>
                        <pre class="bg-light p-2 rounded"><code>php bin/analyze-5250.php --file examples/order-entry.dds</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">🎨 Generate</h5>
                        <p class="card-text">Generate responsive web UIs from your parsed screen definitions with Bootstrap 5 templates.</p>
                        <pre class="bg-light p-2 rounded"><code>php bin/generate-ui.php --input analysis.json --output ./ui</code></pre>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">🚀 Deploy</h5>
                        <p class="card-text">Deploy your modernized UI alongside existing RPG programs or with new PHP backends.</p>
                        <pre class="bg-light p-2 rounded"><code>php -S localhost:8080 -t public</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <h2>Example DDS Files</h2>
        <table class="table">
            <thead>
                <tr><th>File</th><th>Description</th></tr>
            </thead>
            <tbody>
                <tr><td><a href="../examples/order-entry.dds">order-entry.dds</a></td><td>Customer inquiry with subfile</td></tr>
                <tr><td><a href="../examples/item-master.dds">item-master.dds</a></td><td>Item master maintenance screen</td></tr>
            </tbody>
        </table>
    </div>
</body>
</html>
