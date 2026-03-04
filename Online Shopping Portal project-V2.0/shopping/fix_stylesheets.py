import os
import re

# Find all PHP files
for root, dirs, files in os.walk('.'):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            try:
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                original = content
                
                # Remove main red.css link
                content = re.sub(r'\s*<\s*link\s+rel="stylesheet"\s+href="assets/css/red\.css"\s*>\s*\n?', '', content)
                
                # Remove alternate stylesheet color theme links
                content = re.sub(r'\s*<\s*link\s+href="assets/css/(green|blue|red|orange|dark-green)\.css"\s+rel="alternate stylesheet"[^>]*>\s*\n?', '', content)
                
                # Only write if changed
                if content != original:
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(content)
                    print(f"Fixed: {filepath}")
                
            except Exception as e:
                print(f"Error processing {filepath}: {e}")

print("\nAll PHP files cleaned of old color themes")
