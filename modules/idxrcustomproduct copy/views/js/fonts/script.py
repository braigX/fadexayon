import os

# Directory containing font folders
font_dir = "."

# HTML select structure
html_content = "<select id='fontSelect' onchange='changeFont(this.value)'>\n"
html_content += "  <option value='' disabled selected>Select a font</option>\n"

# Loop through each folder
for folder_name in os.listdir(font_dir):
    folder_path = os.path.join(font_dir, folder_name)
    
    # Check if it's a directory
    if os.path.isdir(folder_path):
        # Loop through files in the folder
        for file_name in os.listdir(folder_path):
            # Check for font files
            if file_name.endswith((".ttf", ".otf")):
                font_path = os.path.join(folder_name, file_name)  # Relative path
                font_name = f"{folder_name} - {file_name.split('.')[0]}"  # Display name in select
                
                # Add an option to the select
                html_content += f"  <option value='./fonts/{font_path}'>{font_name}</option>\n"

html_content += "</select>"

# JavaScript function to use OpenType.js
html_content += """
<script src="https://cdnjs.cloudflare.com/ajax/libs/opentype.js/1.3.3/opentype.min.js"></script>
<script>
function changeFont(fontPath) {
    opentype.load(fontPath, function(err, font) {
        if (err) {
            console.error('Font could not be loaded:', err);
        } else {
            console.log('Font loaded:', font);
            // Apply the font to your elements as needed
        }
    });
}
</script>
"""

# Save the HTML content to a file
with open("font_selector.html", "w") as file:
    file.write(html_content)

print("HTML select for fonts created successfully.")
