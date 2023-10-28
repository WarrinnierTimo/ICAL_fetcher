    public function fetchICalData()
    {
        try {
            $iCal_url = "ICAL_LINK";
            $response = Http::get($iCal_url);
            $iCal_data = $response->body();

            // Process the iCal data and extract start and end dates
            $dates = $this->extractDatesFromICal($iCal_data);

            return response()->json(['dates' => $dates]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function extractDatesFromICal($iCalData)
    {
        $lines = explode("\n", $iCalData);

        $inEvent = false;
        $event = [];
        $eventsArray = [];

        foreach ($lines as $line) {
            if (strpos($line, 'BEGIN:VEVENT') === 0) {
                $inEvent = true;
                $event = [];
            } elseif (strpos($line, 'END:VEVENT') === 0) {
                $inEvent = false;
                if (isset($event['DTSTART;VALUE=DATE']) && isset($event['DTEND;VALUE=DATE'])) {
                    $startDate = $this->formatDate($event['DTSTART;VALUE=DATE']);
                    $endDate = $this->formatDate($event['DTEND;VALUE=DATE']);

                    // Create an object for the event
                    $eventData = [
                        'Start Date' => $startDate,
                        'End Date' => $endDate
                    ];

                    $eventsArray[] = $eventData;
                }
            } elseif ($inEvent) {
                // Process event lines and split key and value
                $parts = explode(':', $line, 2);

                if (count($parts) === 2) {
                    $event[$parts[0]] = $parts[1];
                }
            }
        }

        return response()->json(['dates' => $eventsArray]);
    }

    private function formatDate($dateString)
    {
        return substr($dateString, 0, 4) . '-' . substr($dateString, 4, 2) . '-' . substr($dateString, 6, 2);
    }