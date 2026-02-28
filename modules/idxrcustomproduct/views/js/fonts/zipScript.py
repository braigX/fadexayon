import zipfile
import os

# Directory containing zip files
zip_dir = "."

for filename in os.listdir(zip_dir):
    if filename.endswith(".zip"):
        zip_path = os.path.join(zip_dir, filename)
        extract_dir = os.path.join(zip_dir, filename[:-4])  # Folder without .zip extension

        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(extract_dir)
