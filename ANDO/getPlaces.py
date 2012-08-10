from urllib.request import urlopen
from json import loads, dumps
#https://maps.google.com/?ll=51.732158,0.75561&spn=0.015336,0.042272&t=m&z=15
def pinline(x):
	print(x)
	return x
def getJson(url):
	return loads(urlopen(url).read().decode("UTF-8"))
def getBathingPoints(minLat, maxLat, minLng, maxLng, pageSize=10, page=0):
	return getJson(pinline("\
http://environment.data.gov.uk/doc/bathing-water.json?\
min-samplingPoint.lat=%s&\
max-samplingPoint.lat=%s&\
min-samplingPoint.long=%s&\
max-samplingPoint.long=%s&\
_page=%s&\
_pageSize=%s\
	"%(minLat, maxLat, minLng, maxLng, pageSize, page)))
def getBathingPointsAround(lat, lng, radius, pageSize, page):
	minLat=lat-radius
	maxLat=lat+radius
	minLng=lng-radius
	maxLng=lng+radius
	return getBathingPoints(minLat, maxLat, minLng, maxLng, pageSize, page)
def getLastIndex(needle, haystack):
	cnum=0
	for num, item in enumerate(haystack):
		if item == needle:
			cnum=num
	return cnum
def stripURL(url):
	return url[getLastIndex("/", url)+1:]
def convLinkedURL(url):
	"""Converts linked data URLs to their JSON URLs"""
	return url.replace("http://environment.data.gov.uk/id/", "http://environment.data.gov.uk/doc/")+".json"
def getNiceBathingData(lat, lng, radius, pageSize, page):
		return [
		{
			"sediment":			stripURL(place["sedimentTypesPresent"]),
			"yearDesignated":	stripURL(place["yearDesignated"]),
			"name":				place["name"]["_value"],
			"about":			convLinkedURL(place["_about"]),
			"district":	{
				"about":		place["district"][0]["_about"],
				"name":			place["district"][0]["name"]["_value"]
			},
			"lastTest":{
				"results":		convLinkedURL(place["latestSampleAssessment"]["_about"]),
				"verdict":		place["latestSampleAssessment"]["sampleClassification"]["name"]["_value"]
			},
			"type":[stripURL(typedec) for typedec in place["type"]]
		}
		for place in getBathingPointsAround(51.732158,0.75561, 1000, 10, 0)["result"]["items"]
		]
		
print(getNiceBathingData(51.732158,0.75561, 1000, 10, 0))