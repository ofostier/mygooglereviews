from outscraper import ApiClient

api_client = ApiClient(api_key='AIzaSyAm6X8wBA-nm_RJ1Xg3qgUEiUx124hg41o')
reviews_response = api_client.google_maps_business_reviews(
    'krysakids, mallemort', limit=100, language='fr')