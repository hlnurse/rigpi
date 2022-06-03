/*!
 * calendarJs v1.0 is a jQuery extension to display a calendar in a jQuery container
 * https://github.com/martini224/calendarJs
 *
 * Includes jquery.js
 * https://jquery.com/
 *
 * Includes poppbootster.js
 * https://popper.js.org
 *
 * Includes bootstrap.js
 * https://getbootstrap.com/
 *
 * Copyright Martin Rouffiange (martini224) 2018
 * Released under the MIT license (http://opensource.org/licenses/MIT)
 */
(function(e) { e.fn.calendarJs = function (options) {

	var eOptions,
		container = $(this),
        daysNames = initDaysNames(),
        monthNames = initMonthNames(),
        currentDate = new Date(),
        currentYear = null,
        currentMonth = null,
        supportedLanguages = ["fr", "en"],
		defaultLanguage = "en";

    $(document).ready(function() {
        init();
    });

	function init(){
		initOptions();
		changeMonth(0);
		initListeners();
	}

	function initOptions(){
		// handle passed options to calendarJs
        if (options !== null && typeof(options) !== 'undefined') {
            eOptions = {
				lang : (typeof options.lang === 'string' || options.lang instanceof String) && isSupportedLanguage(options.lang) ? options.lang : defaultLanguage,
				withArrows : typeof(options.withArrows) === "boolean" ? options.withArrows : false
            };
        } else {
            eOptions = {
                lang : defaultLanguage,
                withArrows : false
            };
		}
	}

	function isSupportedLanguage(lang){
		return supportedLanguages.find(function(l){return l === lang}) !== undefined;
	}

	function initDaysNames() {
		var daysNames = [];
		daysNames.push({"fr": "Lundi", "en": "Monday"});
		daysNames.push({"fr": "Mardi", "en": "Tuesday"});
		daysNames.push({"fr": "Mercredi", "en": "Wednesday"});
		daysNames.push({"fr": "Jeudi", "en": "Thursday"});
		daysNames.push({"fr": "Vendredi", "en": "Friday"});
		daysNames.push({"fr": "Samedi", "en": "Saturday"});
		daysNames.push({"fr": "Dimanche", "en": "Sunday"});
		return daysNames;
	}

	function initMonthNames() {
		var monthNames = [];
		monthNames.push({"fr": "Janvier", "en": "January"});
		monthNames.push({"fr": "Février", "en": "February"});
		monthNames.push({"fr": "Mars", "en": "March"});
		monthNames.push({"fr": "Avril", "en": "April"});
		monthNames.push({"fr": "Mai", "en": "May"});
		monthNames.push({"fr": "Juin", "en": "June"});
		monthNames.push({"fr": "Juillet", "en": "July"});
		monthNames.push({"fr": "Aout", "en": "August"});
		monthNames.push({"fr": "Septembre", "en": "September"});
		monthNames.push({"fr": "Octobre", "en": "October"});
		monthNames.push({"fr": "Novembre", "en": "November"});
		monthNames.push({"fr": "Décembre", "en": "December"});
		return monthNames;
	}

	function initListeners() {

		container.on('click', '.prev-month', function () {
			prevMonth();
		});

		container.on('click', '.next-month', function () {
			nextMonth();
		});

		if(eOptions.withArrows) {
            $(document).keydown(function (e) {
                switch (e.which) {
                    case 37: // left
                        prevMonth();
                        break;

                    case 38: // up
                        prevYear();
                        break;

                    case 39: // right
                        nextMonth();
                        break;

                    case 40: // down
                        nextYear();
                        break;

                    default:
                        return; // exit this handler for other keys
                }
            });
        }
	}

	function nextMonth() {
		changeMonth(1);
	}

	function prevMonth() {
		changeMonth(-1);
	}

	function nextYear() {
		changeYear(1);
	}

	function prevYear() {
		changeYear(-1);
	}

	function changeMonth(num) {
		currentDate = new Date(currentDate.setMonth(currentDate.getMonth() + num));

		if (currentMonth == null || currentMonth.monthInYear + num < 0 || currentMonth.monthInYear + num > 11) {
			currentYear = createCompleteYear();
		}

		currentMonth = currentYear.months[currentDate.getMonth()];
		var containerToAdd = currentMonth.asTable(currentYear.numYear, eOptions.lang);
		container.html(containerToAdd);
		container.width(containerToAdd.find("table").width());
	}

	function changeYear(num) {
		currentDate = new Date(currentDate.setFullYear(currentDate.getFullYear() + num));
		currentYear = createCompleteYear();
		changeMonth(0);
	}

	function createCompleteYear() {
		var year = new Year(currentDate.getFullYear());
		var iWeek = 1;
		var iDay = 0;

		var day = new Day(new Date(year.numYear, 0, 1, 1, 0, 0, 0));
		while (day.date.getFullYear() === year.numYear) {
			if (day.dayInMonth === 1) {
				year.months.push(new Month(day.date.getMonth()));
			}
			if (iDay-- === 0) {
				iDay = 6;
				year.weeks.push(new Week(iWeek++));
			}

			year.months[year.months.length - 1].days.push(day);
			year.weeks[year.weeks.length - 1].days.push(day);

			day = new Day(new Date(day.date.getTime() + 1000 * 60 * 60 * 24));
		}

		return year;
	}

	function shortenName(name, maxLength) {
		return name.length > maxLength ? name.substr(0, maxLength) : name;
	}

	function getDayName(dayInWeek, lang) {
		return daysNames[dayInWeek][lang];
	}

	function getMonthName(monthInYear, lang) {
		return monthNames[monthInYear][lang];
	}

	function dayInWeekConverter(dayInWeek) {
		return dayInWeek === 0 ? 6 : dayInWeek - 1;
	}

	function dayInWeekUnconverter(dayInWeek) {
		return dayInWeek === 6 ? 0 : dayInWeek + 1;
	}

	function dayIsDate(date1, date2) {
		return date1.getDate() === date2.getDate() && date1.getMonth() === date2.getMonth()
			&& date1.getFullYear() === date2.getFullYear();
	}

    class Day {

        constructor(date) {
            this.date = date;
            this.dayInWeek = dayInWeekConverter(date.getDay());
            this.dayInMonth = date.getDate();
        }
    }

    class Week {

        constructor(weekInYear) {
            this.weekInYear = weekInYear;
            this.days = [];
        }
    }

    class Month {

        constructor(monthInYear) {
            this.monthInYear = monthInYear;
            this.days = [];
        }

        asTable(year, lang) {
            var container = $("<div></div>");
            var now = new Date();

            var monthName = getMonthName(this.monthInYear, lang);
            container.append("<h1>" + monthName + " - " + year + "</h1>");

            var table = $("<table></table>").appendTo(container);
            var tHead = $("<thead></thead>").appendTo(table);
            var tr = $("<tr></tr>").appendTo(tHead);

            for (var iDay = 0; iDay < 7; iDay++) {
                tr.append("<th>" + shortenName(getDayName(iDay, lang), 2) + "</th>");
            }

            var tBody = $("<tbody></tbody>").appendTo(table);


            var iDayInWeek = 6;
            for (iDay = 0; iDay < this.days.length; iDay++) {
                if (iDayInWeek++ === 6) {
                    iDayInWeek = 0;
                    tr = $("<tr></tr>").appendTo(tBody);
                }

                if (this.days[iDay].dayInWeek !== iDayInWeek) {
                    for (var i = 0; i < this.days[iDay].dayInWeek; i++) {
                        tr.append("<td></td>");
                        iDayInWeek++;
                    }
                }
                var dayCompleteDescription = getDayName(this.days[iDay].dayInWeek, lang) + " "
                    + this.days[iDay].dayInMonth + " " + monthName + " " + year;

                var classOfDay = dayIsDate(this.days[iDay].date, now) ? "now" : "";
                tr.append('<td class="' + classOfDay + '">' +
                    '<a title="' + dayCompleteDescription + '">' + this.days[iDay].dayInMonth + '</a></td>');
            }

            if (tBody.find("tr").length === 5) {
                tBody.append('<tr><td class="td-hidden">1</td></tr>')
            }

            container.append('<div class="flex-container"><button type="button" class="btn prev-month"> < </button>' +
                '<span style="flex: 1"></span><button type="button" class="btn next-month"> > </button></div>');

            return container;
        }
    }

    class Year {

        constructor(numYear) {
            this.numYear = numYear;
            this.months = [];
            this.weeks = [];
        }
    }
};}(jQuery));