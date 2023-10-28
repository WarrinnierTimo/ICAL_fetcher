import request from 'request-promise-native';

const iCal_url = "ICAL_LINK";

function formatDate(dateString) {
  const year = dateString.slice(0, 4);
  const month = dateString.slice(4, 6);
  const day = dateString.slice(6, 8);
  return `${year}-${month}-${day}`;
}

async function fetchICalData() {
  try {
    const iCal_data = await request(iCal_url);

    // Split the iCal data into lines
    const lines = iCal_data.split('\n');

    let inEvent = false;
    let event = {};
    const eventsArray = [];

    // Iterate through lines and extract and format start and end dates
    for (const line of lines) {
      if (line.startsWith('BEGIN:VEVENT')) {
        inEvent = true;
        event = {};
      } else if (line.startsWith('END:VEVENT')) {
        inEvent = false;
        if (event['DTSTART;VALUE=DATE'] && event['DTEND;VALUE=DATE']) {
          const startDate = formatDate(event['DTSTART;VALUE=DATE']);
          const endDate = formatDate(event['DTEND;VALUE=DATE']);

          // Create an object for the event
          const eventData = {
            "Start Date": startDate,
            "End Date": endDate
          };

          eventsArray.push(eventData);
        }
      } else if (inEvent) {
        const [key, value] = line.split(':');
        event[key] = value;
      }
    }

    // Convert the array of event data into a JSON string
    const eventsJson = JSON.stringify(eventsArray, null, 2);
    console.log(eventsJson);
  } catch (error) {
    console.error("Error:", error);
  }
}

fetchICalData();