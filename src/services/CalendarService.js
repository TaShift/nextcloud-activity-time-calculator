import axios from 'axios';

export class CalendarService {
    constructor(userId, password) {
        this.userId = userId;
        this.password = password;
        this.baseUrl = window.location.origin + '/remote.php/dav';
    }

    async getCalendarEvents() {
        try {
            console.log('Fetching calendar events for user:', this.userId);
            
            // Get available calendars first
            const calendars = await this.getCalendars();
            console.log('Available calendars:', calendars);
            
            let allEvents = [];
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1); // Last month
            const endDate = new Date(); // Today

            // Format dates for CalDAV (YYYYMMDDTHHMMSSZ)
            const formatCalDAVDate = (date) => {
                return date.toISOString()
                    .replace(/[-:]/g, '')
                    .split('.')[0] + 'Z';
            };

            const timeRange = `
                <c:time-range start="${formatCalDAVDate(startDate)}" 
                              end="${formatCalDAVDate(endDate)}"/>
            `;

            for (const calendar of calendars) {
                try {
                    const calendarUrl = `${this.baseUrl}/calendars/${this.userId}/${calendar.id}/`;
                    console.log(`Fetching events from calendar: ${calendar.name} (${calendar.id})`);
                    
                    const reportBody = `<?xml version="1.0"?>
                        <c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
                            <d:prop>
                                <d:getetag/>
                                <c:calendar-data/>
                            </d:prop>
                            <c:filter>
                                <c:comp-filter name="VCALENDAR">
                                    <c:comp-filter name="VEVENT">
                                        ${timeRange}
                                    </c:comp-filter>
                                </c:comp-filter>
                            </c:filter>
                        </c:calendar-query>`;

                    const response = await axios({
                        method: 'REPORT',
                        url: calendarUrl,
                        headers: {
                            'Content-Type': 'application/xml',
                            'Depth': '1',
                        },
                        data: reportBody,
                        auth: {
                            username: this.userId,
                            password: this.password
                        }
                    });

                    console.log(`Response from ${calendar.name}:`, response.status);
                    
                    if (response.data) {
                        const events = this.parseCalendarData(response.data, calendar.name);
                        allEvents = allEvents.concat(events);
                        console.log(`Found ${events.length} events in ${calendar.name}`);
                    }
                } catch (error) {
                    console.error(`Error fetching events from calendar ${calendar.name}:`, error);
                    continue; // Continue with next calendar
                }
            }

            console.log(`Total events found: ${allEvents.length}`);
            return allEvents;

        } catch (error) {
            console.error('Error in getCalendarEvents:', error);
            throw error;
        }
    }

    async getCalendars() {
        try {
            const url = `${this.baseUrl}/calendars/${this.userId}/`;
            
            const response = await axios({
                method: 'PROPFIND',
                url: url,
                headers: {
                    'Depth': '1',
                    'Content-Type': 'application/xml'
                },
                data: `<?xml version="1.0"?>
                    <d:propfind xmlns:d="DAV:" xmlns:cs="http://calendarserver.org/ns/" xmlns:c="urn:ietf:params:xml:ns:caldav" xmlns:oc="http://owncloud.org/ns">
                        <d:prop>
                            <d:displayname />
                            <d:resourcetype />
                            <cs:getctag />
                            <oc:owner-displayname />
                        </d:prop>
                    </d:propfind>`,
                auth: {
                    username: this.userId,
                    password: this.password
                }
            });

            return this.parseCalendars(response.data);
        } catch (error) {
            console.error('Error fetching calendars:', error);
            return [];
        }
    }

    parseCalendars(xmlData) {
        const calendars = [];
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlData, 'text/xml');
        
        const responses = xmlDoc.getElementsByTagName('d:response');
        
        for (let response of responses) {
            const href = response.getElementsByTagName('d:href')[0]?.textContent;
            const displayName = response.getElementsByTagName('d:displayname')[0]?.textContent;
            const resourceType = response.getElementsByTagName('d:resourcetype')[0];
            
            // Check if it's a calendar (has cal:calendar element)
            if (resourceType && resourceType.getElementsByTagName('cal:calendar').length > 0) {
                if (href && displayName && !href.endsWith('/')) {
                    const calendarId = href.split('/').pop();
                    calendars.push({
                        id: calendarId,
                        name: displayName,
                        url: href
                    });
                }
            }
        }
        
        return calendars;
    }

    parseCalendarData(xmlData, calendarName) {
        const events = [];
        try {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlData, 'text/xml');
            
            const responses = xmlDoc.getElementsByTagName('d:response');
            
            for (let response of responses) {
                const calendarData = response.getElementsByTagName('cal:calendar-data')[0];
                if (calendarData && calendarData.textContent) {
                    const icalData = calendarData.textContent;
                    const parsedEvents = this.parseICalData(icalData, calendarName);
                    events.push(...parsedEvents);
                }
            }
        } catch (error) {
            console.error('Error parsing calendar data:', error);
        }
        
        return events;
    }

    parseICalData(icalData, calendarName) {
        const events = [];
        const lines = icalData.split('\n');
        let currentEvent = null;
        let inEvent = false;

        for (const line of lines) {
            const trimmedLine = line.trim();
            
            if (trimmedLine === 'BEGIN:VEVENT') {
                inEvent = true;
                currentEvent = { calendar: calendarName };
            } else if (trimmedLine === 'END:VEVENT') {
                if (currentEvent) {
                    // Calculate duration and add to events
                    const duration = this.calculateEventDuration(currentEvent);
                    if (duration > 0) {
                        events.push({
                            ...currentEvent,
                            duration: duration
                        });
                    }
                }
                inEvent = false;
                currentEvent = null;
            } else if (inEvent && currentEvent) {
                this.parseEventProperty(trimmedLine, currentEvent);
            }
        }

        return events;
    }

    parseEventProperty(line, event) {
        if (line.startsWith('SUMMARY:')) {
            event.summary = line.substring(8);
        } else if (line.startsWith('DTSTART')) {
            const dateStr = line.includes(':') ? line.split(':')[1] : line.split(';VALUE=DATE:')[1];
            event.start = this.parseICalDate(dateStr);
        } else if (line.startsWith('DTEND')) {
            const dateStr = line.includes(':') ? line.split(':')[1] : line.split(';VALUE=DATE:')[1];
            event.end = this.parseICalDate(dateStr);
        } else if (line.startsWith('DESCRIPTION:')) {
            event.description = line.substring(12);
        } else if (line.startsWith('LOCATION:')) {
            event.location = line.substring(9);
        }
    }

    parseICalDate(dateStr) {
        if (!dateStr) return null;
        
        try {
            // Handle different iCalendar date formats
            if (dateStr.includes('T')) {
                // DateTime format: 20241115T100000Z or 20241115T100000
                const formatted = dateStr.replace(/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})/, '$1-$2-$3T$4:$5:$6');
                return new Date(formatted.endsWith('Z') ? formatted : formatted + 'Z');
            } else {
                // Date-only format: 20241115
                return new Date(dateStr.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
            }
        } catch (error) {
            console.error('Error parsing date:', dateStr, error);
            return null;
        }
    }

    calculateEventDuration(event) {
        if (!event.start || !event.end) {
            return 0;
        }

        try {
            const start = new Date(event.start);
            const end = new Date(event.end);
            
            // Validate dates
            if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                return 0;
            }

            const duration = end.getTime() - start.getTime();
            return duration > 0 ? duration : 0;
        } catch (error) {
            console.error('Error calculating event duration:', error);
            return 0;
        }
    }
}
