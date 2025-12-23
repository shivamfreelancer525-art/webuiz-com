<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Debug: {{ $templateName }} - {{ $pageName }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #4ec9b0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-card {
            background: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4ec9b0;
        }
        .info-card h3 {
            margin: 0 0 10px 0;
            color: #4ec9b0;
            font-size: 14px;
        }
        .info-card p {
            margin: 5px 0;
            font-size: 12px;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.success {
            background: #4ec9b0;
            color: #1e1e1e;
        }
        .status.warning {
            background: #dcdcaa;
            color: #1e1e1e;
        }
        .status.error {
            background: #f48771;
            color: #1e1e1e;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .tab {
            padding: 10px 20px;
            background: #2d2d2d;
            border: none;
            color: #d4d4d4;
            cursor: pointer;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background: #1e1e1e;
            color: #4ec9b0;
        }
        .tab-content {
            display: none;
            background: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 5px;
            padding: 20px;
            max-height: 600px;
            overflow: auto;
        }
        .tab-content.active {
            display: block;
        }
        .preview-frame {
            width: 100%;
            height: 800px;
            border: 2px solid #4ec9b0;
            border-radius: 5px;
            background: white;
        }
        pre {
            margin: 0;
            padding: 15px;
            background: #252526;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.5;
        }
        code {
            color: #d4d4d4;
        }
        .highlight {
            background: #264f78;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Template Debug: {{ $templateName }} / {{ $pageName }}</h1>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Template Info</h3>
                    <p><strong>Name:</strong> {{ $templateName }}</p>
                    <p><strong>Page:</strong> {{ $pageName }}</p>
                    <p><strong>Base URL:</strong> <code>{{ $baseUrl }}</code></p>
                </div>
                <div class="info-card">
                    <h3>Preloader Status</h3>
                    <p><strong>Has Preloader:</strong> 
                        <span class="status {{ $hasPreloader ? 'warning' : 'success' }}">
                            {{ $hasPreloader ? 'YES' : 'NO' }}
                        </span>
                    </p>
                    <p><strong>Fix Script Injected:</strong> 
                        <span class="status {{ $preloaderFixInjected ? 'success' : 'error' }}">
                            {{ $preloaderFixInjected ? 'YES' : 'NO' }}
                        </span>
                    </p>
                </div>
                <div class="info-card">
                    <h3>HTML Stats</h3>
                    <p><strong>Original Size:</strong> {{ number_format(strlen($originalHtml)) }} bytes</p>
                    <p><strong>Modified Size:</strong> {{ number_format(strlen($modifiedHtml)) }} bytes</p>
                    <p><strong>Has Base Tag:</strong> 
                        <span class="status {{ strpos($modifiedHtml, '<base') !== false ? 'success' : 'error' }}">
                            {{ strpos($modifiedHtml, '<base') !== false ? 'YES' : 'NO' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('preview')">Preview</button>
            <button class="tab" onclick="showTab('modified')">Modified HTML</button>
            <button class="tab" onclick="showTab('original')">Original HTML</button>
            <button class="tab" onclick="showTab('analysis')">Analysis</button>
        </div>

        <div id="preview" class="tab-content active">
            <h3>Rendered Preview</h3>
            <iframe src="{{ url('/templates/preview/' . $templateName . '/' . $pageName) }}" class="preview-frame"></iframe>
        </div>

        <div id="modified" class="tab-content">
            <h3>Modified HTML (with fixes)</h3>
            <pre><code>{{ htmlspecialchars($modifiedHtml) }}</code></pre>
        </div>

        <div id="original" class="tab-content">
            <h3>Original HTML (from file)</h3>
            <pre><code>{{ htmlspecialchars($originalHtml) }}</code></pre>
        </div>

        <div id="analysis" class="tab-content">
            <h3>Analysis</h3>
            <div class="info-card">
                <h3>Preloader Detection</h3>
                <p>Found preloader: <code>{{ $hasPreloader ? 'id="preloader"' : 'None' }}</code></p>
                @if($hasPreloader)
                    <p>Preloader position in HTML: 
                        @php
                            $pos = strpos($modifiedHtml, 'id="preloader"');
                            if ($pos === false) $pos = strpos($modifiedHtml, "id='preloader'");
                        @endphp
                        @if($pos !== false)
                            Character {{ number_format($pos) }}
                        @else
                            Not found
                        @endif
                    </p>
                @endif
            </div>
            <div class="info-card">
                <h3>Script Injection</h3>
                <p>Fix script found: 
                    <span class="status {{ strpos($modifiedHtml, 'hidePreloader') !== false ? 'success' : 'error' }}">
                        {{ strpos($modifiedHtml, 'hidePreloader') !== false ? 'YES' : 'NO' }}
                    </span>
                </p>
                @if(strpos($modifiedHtml, 'hidePreloader') !== false)
                    @php
                        $scriptPos = strpos($modifiedHtml, 'hidePreloader');
                        $context = substr($modifiedHtml, max(0, $scriptPos - 200), 400);
                    @endphp
                    <p>Script context:</p>
                    <pre><code>{{ htmlspecialchars($context) }}</code></pre>
                @endif
            </div>
            <div class="info-card">
                <h3>Base Tag</h3>
                @php
                    preg_match('/<base\s+href=["\']([^"\']+)["\']/', $modifiedHtml, $baseMatches);
                @endphp
                @if(!empty($baseMatches))
                    <p>Base URL: <code>{{ $baseMatches[1] }}</code></p>
                    <p>Is Absolute: 
                        <span class="status {{ (strpos($baseMatches[1], 'http://') === 0 || strpos($baseMatches[1], 'https://') === 0) ? 'success' : 'warning' }}">
                            {{ (strpos($baseMatches[1], 'http://') === 0 || strpos($baseMatches[1], 'https://') === 0) ? 'YES' : 'NO' }}
                        </span>
                    </p>
                @else
                    <p class="status error">Base tag not found!</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>



