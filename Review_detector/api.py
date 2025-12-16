from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import os


BASE_DIR = os.path.dirname(os.path.abspath(__file__))

VECTORIZER_PATH = os.path.join(BASE_DIR, "vectorizer.pkl")
MODEL_PATH = os.path.join(BASE_DIR, "model.pkl")

try:
    vectorizer = joblib.load(VECTORIZER_PATH)
except Exception as e:
    raise RuntimeError(f"Failed to load vectorizer from {VECTORIZER_PATH}: {e}")

try:
    model = joblib.load(MODEL_PATH)
except Exception as e:
    raise RuntimeError(f"Failed to load model from {MODEL_PATH}: {e}")


app = FastAPI(title="Boighor Review Manipulation Detector")

class ReviewInput(BaseModel):
    text: str


@app.get("/")
def health_check():
    """Simple health endpoint to test if API is running."""
    return {"status": "ok", "message": "Boighor review detector is running"}


@app.post("/predict")
def predict(review: ReviewInput):
    """
    Input : review text
    Output: score (0..1, probability of fake) + label
    """
    try:
        # 4. Transform text using TF-IDF vectorizer
        X = vectorizer.transform([review.text])

        # Assuming class 1 = fake/manipulated
        proba_fake = float(model.predict_proba(X)[0][1])

        label = "fake" if proba_fake >= 0.5 else "genuine"

        return {
            "score": proba_fake,   # 0..1
            "label": label
        }
    except Exception as e:
        # If something goes wrong during prediction
        raise HTTPException(status_code=500, detail=f"Prediction error: {e}")

