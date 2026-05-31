import sys

try:
    import PyPDF2
    with open(sys.argv[1], 'rb') as file:
        reader = PyPDF2.PdfReader(file)
        text = ""
        for page in reader.pages:
            text += page.extract_text() + "\n"
        print(text[:3000]) # Print first 3000 chars to see what's in it
except ImportError:
    print("PyPDF2 not installed. Try installing it: pip install PyPDF2")
except Exception as e:
    print(f"Error: {e}")
