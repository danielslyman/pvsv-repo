# ChangeLog

## 2.5.5
* ADD: Some WPML compatibility;
* UPD: Datepicker field templates;
* FIX: Calendar proper label in tooltip;
* FIX: Excluded Dates option handle in script and admin area datepicker;
* FIX: Default and filtered datepicker field values;
* FIX: Weeks offset functionality;
* FIX: Booking functionality for different languages;

## 2.5.4
* ADD: `jquery-date-range-picker` in to dashboard edit & add booking popup;
* ADD: `'jet-booking/google-calendar-url/utc-timezone'` hook for timezone manipulation in Google calendar event link;
* ADD: `'jet-booking/form-fields/check-in-out/default-value'` hook for default `check-in-out` field value;
* UPD: Booking admin popups templates;
* FIX: Advanced price rates default value.

## 2.5.3
* ADD: JS filter `'jet-booking/calendar/config'` for calendar widget config;
* ADD: JS filter `'jet-booking/apartment-price'` for apartment price;
* FIX: Booking calendar layout;

## 2.5.2
* ADD: Dynamic tags: Available units count, Bookings count;
* ADD: Additional custom labels;
* UPD: Allow filtering settings value before return with `'jet-booking/settings/get/' . $setting-key`;
* UPD: Custom labels default value initialization;
* FIX: Order of advanced prices application;
* FIX: `check-in-out` field searched dates;
* FIX: Dynamic tag price per day/night;
* FIX: Advanced price popups data duplication;
* FIX: Filter result with Checkout only option.

## 2.5.1
* ADD: Checkout only days option;
* ADD: `jet-booking/form-action/pre-process` hook to allow handle booking from 3rd party plugin or theme;
* UPD: Update error message in admin popups;
* FIX: Overlapping bookings issue while update booking in admin area;
* FIX: Price rates popups overlays;
* FIX: JetEngine form while plugin setup;
* FIX: Booking list pagination;
* FIX: Minor WooCommerce integration errors;
* FIX: Compatibility with Elementor 3.7.

## 2.5.0
* ADD: Creating booking from admin area;
* ADD: Days off functionality;
* ADD: Disable weekdays and weekends functionality;
* UPD: Admin Booking page popups;
* FIX: One day booking seasonal price;
* FIX: iCal sync wrong check out date;
* FIX: Searched dates display in date fields with One day booking option;
* FIX: Admin Calendar page styles.

## 2.4.6
* FIX: minor JS/PHP issue

## 2.4.5
* FIX: Per Day booking type same dateCheck-in and Check-out.

## 2.4.4
* ADD: Cookies filters searched date store type;
* UPD: WooCommerce order booking details in admin area;
* FIX: Seasonal prising empty rates issue;
* FIX: Booking apartment unit ID;
* FIX: Cron iCal interval synchronization;
* FIX: Default WC product ordering with JetBooking integration;
* FIX: JetBooking dynamic tags;
* FIX: Date range filed in popup after ajax call;
* FIX: Items with units booked dates using per day booking period;
* FIX: Edit&Details popups view in booking list page;
* FIX: Calendar widget editor render;
* FIX: Session filters searched date store type.

## 2.4.3
* FIX: apply units;
* FIX: returning a string instead of output;
* FIX: get_booked_apartments ignore apartments with invalid statuses;
* FIX: Elementor 3.6 compatibility.

## 2.4.2
* FIX: First day of the week

## 2.4.1
* FIX: Translation strings
* FIX: Seasonal prices without post editor

## 2.4.0
* ADD: Seasonal prices

## 2.3.5
* FIX:Synchronizing calendars

## 2.3.4
* FIX:Error of check-in-out fields when submitting a form

## 2.3.3
* FIX: JetFormBuilder compatibility

## 2.3.2
* FIX: Price per 1 day/night

## 2.3.1
* FIX: iCal compatibility

## 2.3.0
* ADD: JetFormBuilder plugin compatibility

## 2.2.6
* FIX: Display of booked days in the calendar

## 2.2.5
* FIX: check in - check out field

## 2.2.4
* ADD: Default apartment price value
* FIX: Booking Availability Calendar

## 2.2.3
* FIX: Init check-out field

## 2.2.2
* FIX: Placeholder in check-out field
* FIX: Option per nights. When the option is enabled, 1 day cannot be booked as the beginning and end of the booking
* FIX: Fixed calendar on mobile device
* FIX: Plugin localization

## 2.2.1
* FIX: Check-in/check-out field in booking form

## 2.2.0
* FIX: iCal post count
* ADD: Select the first day of the week
* ADD: compatibility with php 5.6 +

## 2.1.2
* UPD: Added localization file

## 2.1.1
* FIX: WC product creation.

## 2.1.0
* ADD: Added Booking Availability Calendar widget;
* ADD: Allow to add booking details to WooCommerce checkout fields;
* ADD: Added Property Rates Based on the length of stay;
* ADD: Allow to add booking details to WooCommerce orders;
* ADD: Allow ability for users to add a booking to their calendar.
* FIX: Fixed minor bugs.
