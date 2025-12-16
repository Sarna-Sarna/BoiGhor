import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
import joblib

# ------------------------------------------
# 1. Load Training Data
# ------------------------------------------
# Create a CSV file named dataset.csv with columns:
# text , label
# label = 1 → fake/manipulated
# label = 0 → real review

df = pd.read_csv("dataset.csv")

X = df["text"]
y = df["label"]

# ------------------------------------------
# 2. Convert Text to TF-IDF Features
# ------------------------------------------
vectorizer = TfidfVectorizer(stop_words='english')
X_vec = vectorizer.fit_transform(X)

# ------------------------------------------
# 3. Train/Test Split
# ------------------------------------------
X_train, X_test, y_train, y_test = train_test_split(
    X_vec, y, test_size=0.2, random_state=42
)

# ------------------------------------------
# 4. Train ML Model
# ------------------------------------------
model = LogisticRegression()
model.fit(X_train, y_train)

# ------------------------------------------
# 5. Evaluate Accuracy
# ------------------------------------------
preds = model.predict(X_test)
acc = accuracy_score(y_test, preds)
print("Model Accuracy:", acc)

# ------------------------------------------
# 6. Save Model and Vectorizer
# ------------------------------------------
joblib.dump(model, "model.pkl")
joblib.dump(vectorizer, "vectorizer.pkl")

print("Model and vectorizer saved successfully.")
