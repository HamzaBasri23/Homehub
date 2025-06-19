from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import pandas as pd
import joblib

app = FastAPI()

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Replace "*" with specific origins if needed
    allow_credentials=True,
    allow_methods=["*"],  # Allow all HTTP methods
    allow_headers=["*"],  # Allow all headers
)

# Load model and scaler
model = joblib.load("loan_status_predictor.pkl")
scaler = joblib.load("vector.pkl")

# Define input schema
class LoanApproval(BaseModel):
    Gender: str
    Married: str
    Dependents: str
    Education: str
    Self_Employed: str
    ApplicantIncome: float
    CoapplicantIncome: float
    LoanAmount: float
    Loan_Amount_Term: float
    Credit_History: float
    Property_Area: str

# Encoding mappings
gender_map = {'Male': 1, 'Female': 0}
married_map = {'Yes': 1, 'No': 0}
education_map = {'Graduate': 1, 'Not Graduate': 0}
self_employed_map = {'Yes': 1, 'No': 0}
property_area_map = {'Urban': 2, 'Semiurban': 1, 'Rural': 0}
dependents_map = {'0': 0, '1': 1, '2': 2, '3+': 3}

@app.post("/predict")
async def predict_loan_status(application: LoanApproval):
    try:
        # Convert input into DataFrame
        input_dict = application.dict()

        # Manual Encoding
        input_data = pd.DataFrame([{
            'Gender': gender_map.get(input_dict['Gender'], 0),
            'Married': married_map.get(input_dict['Married'], 0),
            'Dependents': dependents_map.get(input_dict['Dependents'], 0),
            'Education': education_map.get(input_dict['Education'], 0),
            'Self_Employed': self_employed_map.get(input_dict['Self_Employed'], 0),
            'ApplicantIncome': input_dict['ApplicantIncome'],
            'CoapplicantIncome': input_dict['CoapplicantIncome'],
            'LoanAmount': input_dict['LoanAmount'],
            'Loan_Amount_Term': input_dict['Loan_Amount_Term'],
            'Credit_History': input_dict['Credit_History'],
            'Property_Area': property_area_map.get(input_dict['Property_Area'], 0),
        }])

        # Scale numerical features
        num_cols = ['ApplicantIncome', 'CoapplicantIncome', 'LoanAmount', 'Loan_Amount_Term']
        input_data[num_cols] = scaler.transform(input_data[num_cols])

        # Make prediction
        prediction = model.predict(input_data)[0]

        result = "Approved" if prediction == 1 else "Not Approved"
        return {"Loan Status": result}
    
    except Exception as e:
        return {"error": str(e)}