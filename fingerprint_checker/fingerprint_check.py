import cv2
import numpy as np
import requests
import os
from PIL import Image
from io import BytesIO

# Compare deux images via histogramme (simplifié)
def compare_images(img1, img2):
    img1 = cv2.resize(img1, (200, 200))
    img2 = cv2.resize(img2, (200, 200))
    hist1 = cv2.calcHist([img1], [0], None, [256], [0,256])
    hist2 = cv2.calcHist([img2], [0], None, [256], [0,256])
    score = cv2.compareHist(hist1, hist2, cv2.HISTCMP_CORREL)
    return score

# Image à vérifier (ex: capture utilisateur)
def load_uploaded_image(path):
    return cv2.imread(path, cv2.IMREAD_GRAYSCALE)

# Liste des empreintes enregistrées via API Laravel
def fetch_dataset_from_laravel():
    response = requests.get('http://localhost:8000/api/fingerprints')  # JSON: [{"agent_id": 1, "image_url": "..."}, ...]
    return response.json()

# Télécharger une image depuis Laravel
def download_image(url):
    r = requests.get(url)
    return cv2.imdecode(np.frombuffer(r.content, np.uint8), cv2.IMREAD_GRAYSCALE)

# Envoi de présence vers Laravel
def mark_presence(agent_id):
    r = requests.post('http://localhost:8000/api/presences', json={"agent_id": agent_id})
    print("Présence enregistrée pour agent", agent_id, "=>", r.status_code)

# MAIN
def main():
    img_uploaded = load_uploaded_image('empreinte_test.jpg')
    dataset = fetch_dataset_from_laravel()

    for data in dataset:
        img_known = download_image(data['image_url'])
        score = compare_images(img_uploaded, img_known)
        print(f"Comparaison avec agent {data['agent_id']} : score = {score}")
        if score > 0.90:  # Seuil ajustable
            mark_presence(data['agent_id'])
            break
    else:
        print("Aucune correspondance trouvée")

if __name__ == "__main__":
    main()
