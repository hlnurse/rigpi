/*!
FullCalendar v5.10.1
Docs & License: https://fullcalendar.io/
(c) 2021 Adam Shaw
*/
var FullCalendarRRule = (function (exports, common, rrule) {
  "use strict";

  /*! *****************************************************************************
	Copyright (c) Microsoft Corporation.

	Permission to use, copy, modify, and/or distribute this software for any
	purpose with or without fee is hereby granted.

	THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
	REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
	INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
	LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
	OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
	PERFORMANCE OF THIS SOFTWARE.
	***************************************************************************** */

  var __assign = function () {
    __assign =
      Object.assign ||
      function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s)
            if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
        }
        return t;
      };
    return __assign.apply(this, arguments);
  };

  var RRULE_EVENT_REFINERS = {
    rrule: common.identity,
    exrule: common.identity,
    exdate: common.identity,
    duration: common.createDuration,
  };

  var recurring = {
    parse: function (eventProps, dateEnv) {
      if (eventProps.rrule != null) {
        var eventRRuleData = parseEventRRule(eventProps, dateEnv);
        if (eventRRuleData) {
          return {
            typeData: {
              rruleSet: eventRRuleData.rruleSet,
              isTimeZoneSpecified: eventRRuleData.isTimeZoneSpecified,
            },
            allDayGuess: !eventRRuleData.isTimeSpecified,
            duration: eventProps.duration,
          };
        }
      }
      return null;
    },
    expand: function (eventRRuleData, framingRange, dateEnv) {
      var dates;
      if (eventRRuleData.isTimeZoneSpecified) {
        dates = eventRRuleData.rruleSet
          .between(
            dateEnv.toDate(framingRange.start), // rrule lib will treat as UTC-zoned
            dateEnv.toDate(framingRange.end), // (same)
            true
          )
          .map(function (date) {
            return dateEnv.createMarker(date);
          }); // convert UTC-zoned-date to locale datemarker
      } else {
        // when no timezone in given start/end, the rrule lib will assume UTC,
        // which is same as our DateMarkers. no need to manipulate
        dates = eventRRuleData.rruleSet.between(
          framingRange.start,
          framingRange.end,
          true
        );
      }
      return dates;
    },
  };
  var plugin = common.createPlugin({
    recurringTypes: [recurring],
    eventRefiners: RRULE_EVENT_REFINERS,
  });
  function parseEventRRule(eventProps, dateEnv) {
    var rruleSet;
    var isTimeSpecified = false;
    var isTimeZoneSpecified = false;
    if (typeof eventProps.rrule === "string") {
      var res = parseRRuleString(eventProps.rrule);
      rruleSet = res.rruleSet;
      isTimeSpecified = res.isTimeSpecified;
      isTimeZoneSpecified = res.isTimeZoneSpecified;
    }
    if (typeof eventProps.rrule === "object" && eventProps.rrule) {
      // non-null object
      var res = parseRRuleObject(eventProps.rrule, dateEnv);
      rruleSet = new rrule.RRuleSet();
      rruleSet.rrule(res.rrule);
      isTimeSpecified = res.isTimeSpecified;
      isTimeZoneSpecified = res.isTimeZoneSpecified;
    }
    // convery to arrays. TODO: general util?
    var exdateInputs = [].concat(eventProps.exdate || []);
    var exruleInputs = [].concat(eventProps.exrule || []);
    for (
      var _i = 0, exdateInputs_1 = exdateInputs;
      _i < exdateInputs_1.length;
      _i++
    ) {
      var exdateInput = exdateInputs_1[_i];
      var res = common.parseMarker(exdateInput);
      isTimeSpecified = isTimeSpecified || !res.isTimeUnspecified;
      isTimeZoneSpecified = isTimeZoneSpecified || res.timeZoneOffset !== null;
      rruleSet.exdate(
        new Date(res.marker.valueOf() - (res.timeZoneOffset || 0) * 60 * 1000)
      );
    }
    // TODO: exrule is deprecated. what to do? (https://icalendar.org/iCalendar-RFC-5545/a-3-deprecated-features.html)
    for (
      var _a = 0, exruleInputs_1 = exruleInputs;
      _a < exruleInputs_1.length;
      _a++
    ) {
      var exruleInput = exruleInputs_1[_a];
      var res = parseRRuleObject(exruleInput, dateEnv);
      isTimeSpecified = isTimeSpecified || res.isTimeSpecified;
      isTimeZoneSpecified = isTimeZoneSpecified || res.isTimeZoneSpecified;
      rruleSet.exrule(res.rrule);
    }
    return {
      rruleSet: rruleSet,
      isTimeSpecified: isTimeSpecified,
      isTimeZoneSpecified: isTimeZoneSpecified,
    };
  }
  function parseRRuleObject(rruleInput, dateEnv) {
    var isTimeSpecified = false;
    var isTimeZoneSpecified = false;
    function processDateInput(dateInput) {
      if (typeof dateInput === "string") {
        var markerData = common.parseMarker(dateInput);
        if (markerData) {
          isTimeSpecified = isTimeSpecified || !markerData.isTimeUnspecified;
          isTimeZoneSpecified =
            isTimeZoneSpecified || markerData.timeZoneOffset !== null;
          return new Date(
            markerData.marker.valueOf() -
              (markerData.timeZoneOffset || 0) * 60 * 1000
          ); // NOT DRY
        }
        return null;
      }
      return dateInput; // TODO: what about number timestamps?
    }
    var rruleOptions = __assign(__assign({}, rruleInput), {
      dtstart: processDateInput(rruleInput.dtstart),
      until: processDateInput(rruleInput.until),
      freq: convertConstant(rruleInput.freq),
      wkst:
        rruleInput.wkst == null
          ? (dateEnv.weekDow - 1 + 7) % 7 // convert Sunday-first to Monday-first
          : convertConstant(rruleInput.wkst),
      byweekday: convertConstants(rruleInput.byweekday),
    });
    return {
      rrule: new rrule.RRule(rruleOptions),
      isTimeSpecified: isTimeSpecified,
      isTimeZoneSpecified: isTimeZoneSpecified,
    };
  }
  function parseRRuleString(str) {
    var rruleSet = rrule.rrulestr(str, { forceset: true });
    var analysis = analyzeRRuleString(str);
    return __assign({ rruleSet: rruleSet }, analysis);
  }
  function analyzeRRuleString(str) {
    var isTimeSpecified = false;
    var isTimeZoneSpecified = false;
    function processMatch(whole, introPart, datePart) {
      var result = common.parseMarker(datePart);
      isTimeSpecified = isTimeSpecified || !result.isTimeUnspecified;
      isTimeZoneSpecified =
        isTimeZoneSpecified || result.timeZoneOffset !== null;
    }
    str.replace(/\b(DTSTART:)([^\n]*)/, processMatch);
    str.replace(/\b(EXDATE:)([^\n]*)/, processMatch);
    str.replace(/\b(UNTIL=)([^;\n]*)/, processMatch);
    return {
      isTimeSpecified: isTimeSpecified,
      isTimeZoneSpecified: isTimeZoneSpecified,
    };
  }
  function convertConstants(input) {
    if (Array.isArray(input)) {
      return input.map(convertConstant);
    }
    return convertConstant(input);
  }
  function convertConstant(input) {
    if (typeof input === "string") {
      return rrule.RRule[input.toUpperCase()];
    }
    return input;
  }

  common.globalPlugins.push(plugin);

  exports.default = plugin;

  Object.defineProperty(exports, "__esModule", { value: true });

  return exports;
})({}, FullCalendar, rrule);
