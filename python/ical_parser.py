from ics import Calendar
import requests

# Retrieve the iCal data from the link
iCal_url = "ICAL_LINK"
response = requests.get(iCal_url)
iCal_data = response.text

# Parse the iCal data
c = Calendar(iCal_data)

# Iterate through events and extract details
for event in c.events:
    print("Summary:", event.name)
    print("Start Date:", event.begin.date())
    print("End Date:", event.end.date())
    print("Description:", event.description)
    print("UID:", event.uid)
    print("\n")