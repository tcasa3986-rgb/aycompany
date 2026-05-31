import markdown

with open('Documentacion.md', 'r', encoding='utf-8') as f:
    text = f.read()

html = markdown.markdown(text, extensions=['tables', 'fenced_code'])

full_html = f"""
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Documentación MiniMarket</title>
<style>
body {{
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #2c3e50;
    max-width: 850px;
    margin: 0 auto;
    padding: 40px;
}}
h1, h2, h3 {{
    color: #e67e22; /* matching possible brand color */
}}
h1 {{
    border-bottom: 3px solid #f39c12;
    padding-bottom: 10px;
    text-align: center;
}}
h2 {{
    border-bottom: 2px solid #eee;
    padding-bottom: 5px;
    margin-top: 40px;
}}
ul, ol {{
    margin-bottom: 20px;
}}
li {{
    margin-bottom: 8px;
}}
code {{
    background-color: #f8f9fa;
    padding: 3px 6px;
    border-radius: 4px;
    font-family: Consolas, monospace;
    font-size: 0.9em;
    color: #e74c3c;
}}
a {{
    color: #3498db;
    text-decoration: none;
}}
hr {{
    border: 0;
    border-top: 1px solid #ddd;
    margin: 40px 0;
}}
img {{
    max-width: 100%;
    display: block;
    margin: 0 auto;
}}
</style>
</head>
<body>
{html}
</body>
</html>
"""

with open('Documentacion.html', 'w', encoding='utf-8') as f:
    f.write(full_html)
