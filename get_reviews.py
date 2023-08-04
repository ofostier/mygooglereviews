import googlemaps 
import pandas as pd

apikey = input("Enter your API KEY: ")
gmaps = googlemaps.Client(key=apikey)

place_name = 'krysakids, mallemort france' 
print(place_name)

place_details = gmaps.places(place_name) 
place_details_id = place_details['results'][0]['place_id']
print(place_details_id)


place = gmaps.place(place_details_id, 'fr') 
print(place['result']['reviews'])


