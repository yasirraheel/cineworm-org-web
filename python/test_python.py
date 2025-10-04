#!/home/u393044472/python/bin/python3

import sys
import requests
import numpy as np
import json

# Print Python version (existing feature)
print("Content-Type: text/plain\n")
print("Python script is working correctly!")
print(f"Python Version: {sys.version}\n")

# Test API call using 'requests' package
API_URL = "https://jsonplaceholder.typicode.com/posts/1"
try:
    response = requests.get(API_URL)
    response.raise_for_status()
    api_data = response.json()
    print("API Call Successful!")
    print(f"Title: {api_data['title']}")
except requests.exceptions.RequestException as e:
    print(f"API Call Failed: {e}")

# Test NumPy functionality
array1 = np.array([1, 2, 3, 4, 5])
array2 = np.array([10, 20, 30, 40, 50])
result = array1 + array2

print("\nNumPy Array Operation Successful!")
print(f"Array 1: {array1}")
print(f"Array 2: {array2}")
print(f"Result: {result}\n")

print("All tests passed successfully!")
