import os
import json

# Directory containing font folders
font_dir = "C:/Users/Braig/Documents/novatice/New folder (2)"

# Data structure to hold fonts organized by folder
font_data = {}

# Loop through each folder
for folder_name in os.listdir(font_dir):
    folder_path = os.path.join(font_dir, folder_name)
    
    # Check if it's a directory
    if os.path.isdir(folder_path):
        font_files = []
        # Loop through files in the folder
        for file_name in os.listdir(folder_path):
            # Check for font files
            if file_name.endswith((".ttf", ".otf")):
                font_files.append(file_name)
        
        # Add folder and its fonts to the data structure
        if font_files:
            font_data[folder_name] = font_files

# HTML structure for folder and font selection
html_content = """
<select id="folderSelect" onchange="updateFonts(this.value)">
  <option value="" disabled selected>Select a folder</option>
"""
# Populate the folder select options
for folder_name in font_data:
    html_content += f"  <option value='{folder_name}'>{folder_name}</option>\n"
html_content += "</select>\n"

# Placeholder for the font select, which will update based on folder choice
html_content += """
<select id="fontSelect" onchange="changeFont(this.value)">
  <option value="" disabled selected>Select a font</option>
</select>

<script src="https://cdnjs.cloudflare.com/ajax/libs/opentype.js/1.3.3/opentype.min.js"></script>
<script>
const fontData = JSON.parse(`{}`);  // Placeholder for font data in JSON format

// Function to update font options based on selected folder
function updateFonts(folder) {
    const fontSelect = document.getElementById("fontSelect");
    fontSelect.innerHTML = "<option value='' disabled selected>Select a font</option>";
    
    if (folder && fontData[folder]) {
        fontData[folder].forEach(font => {
            fontSelect.innerHTML += `<option value='${folder}/${font}'>${font.split('.')[0]}</option>`;
        });
    }
}

// Function to load and apply the font using OpenType.js
function changeFont(fontPath) {
    if (fontPath) {
        opentype.load(fontPath, function(err, font) {
            if (err) {
                console.error('Font could not be loaded:', err);
            } else {
                console.log('Font loaded:', font);
                // Apply the font to your elements as needed
            }
        });
    }
}
</script>
""".replace("{}", json.dumps(font_data))  # Insert font data as JSON

# Save the HTML content to a file
with open("font_selector.html", "w") as file:
    file.write(html_content)

print("HTML file with nested selects created successfully.")
