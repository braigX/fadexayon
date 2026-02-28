/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

"use strict";

/**
 * Geodis Map manager
 */

/**
 * @param $mapContainer GeodisJQuery element
 * @param options object:
 *   - bool draggable
 *   - bool commandPanel
 */
var GeodisMap = function($mapContainer, options, GeodisJQuery) {
    this.callbackListeners = [];
    this.mapLoadedListeners = [];
    this.markers = [];
    this.options = options;
    this.isLoaded = false;
    this.GeodisJQuery = GeodisJQuery;

    this.$map = $mapContainer;

    this.defaultOptions = {
        draggable: true,
        commandPanel: false
    };
    this.countryCode = options.countryCode;

    if (typeof this.options == 'undefined') {
        this.options = [];
    }
    for (var option in this.defaultOptions) {
        if (typeof(this.options[option]) == 'undefined') {
            this.options[option] = this.defaultOptions[option];
        }
    }

    google.maps.event.addDomListener(window, 'load', (function() {
        this.init();
    }).bind(this));

    this.init();
    return this;
};

GeodisMap.prototype.init = function() {
    var mapOptions = {
        center: {lat: 0, lng: 0},
        zoom: 8,
        draggable: this.options.draggable,
        mapTypeControl: false,
        panControl: this.options.commandPanel,
        streetViewControl: this.options.commandPanel,
        zoomCommand: this.options.commandPanel
    };
    this.map = new google.maps.Map(this.$map.get(0), mapOptions);
    this.geocoder = new google.maps.Geocoder();

    this.isLoaded = true;
    this.mapLoadedListeners.forEach(function(callback) {
        callback();
    });

    this.listen();
};

/**
 * Refresh map after resizing
 */
GeodisMap.prototype.refresh = function() {
    google.maps.event.trigger(this.map, 'resize');
    return this;
};

/**
 * Define map center
 */
GeodisMap.prototype.setCenter = function(latitude, longitude) {
    var center = this.map.getCenter();
    if (center.lat() == latitude || center.lng() == longitude) {
        return this;
    }

    this.map.setCenter(new google.maps.LatLng(latitude, longitude));
    return this;
};

/**
 * Centrer the maps on the customer position
 */
GeodisMap.prototype.autoCenter = function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((function(position) {
            this.setCenter(position.coords.latitude, position.coords.longitude);
            this.setZoom(15);
        }).bind(this));
    }
    return this;
};

/**
 * Localize customer
 */
GeodisMap.prototype.getLocation = function() {
    var deferred = this.GeodisJQuery.Deferred(),
        onDoneGeoloc = function(pos) {
            this.getAddress(pos, deferred);
        },
        onFailedGeoloc = function(errorObj) {
            deferred.reject(errorObj.message);
        };
    if ("geolocation" in navigator) {
        // get address from Lat-Long coordinates
        navigator.geolocation.getCurrentPosition(onDoneGeoloc.bind(this), onFailedGeoloc, {timeout: 3000, maximumAge: 0});
    } else {
        deferred.reject("Geolocation is not supported by this browser.");
    }
    return deferred.promise();
};

/**
 * Get address from Lat-long coordinates
 * @param position Geolocation
 * @param deferred this.GeodisJQuery.Deferred()
 */
GeodisMap.prototype.getAddress = function(position, deferred) {
    var geocoder = new google.maps.Geocoder(); // create a geocoder object
    var location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); // turn coordinates into an object

    geocoder.geocode({'latLng': location}, function(results, status) {
        // if geocode success
        if (status == google.maps.GeocoderStatus.OK) {
            var geoAddress = {"formatted_address": results[0].formatted_address, "street_number": "", "street": "", "city": "", "postal_code": "", "country": ""};
            this.GeodisJQuery.each(results[0].address_components, function(i, item) {
                if (item.types[0] == "street_number") {
                    geoAddress.street_number = item.long_name;
                }
                if (item.types[0] == "route") {
                    geoAddress.street = item.long_name;
                }
                if (item.types[0] == "postal_code") {
                    geoAddress.postal_code = item.long_name;
                }
                if (item.types[0] == "locality") {
                    geoAddress.city = item.long_name;
                }
                if (item.types[0] == "country") {
                    geoAddress.country = item.short_name;
                }
            });
            deferred.resolve(geoAddress);
        } else {
            // alert any other error(s)
            alert("Geocode failure: " + status);
            return false;
        }
    });
};

/**
 * Define zoom
 */
GeodisMap.prototype.setZoom = function(zoom) {
    this.map.setZoom(zoom);
    return this;
};

/**
 * Add listener on map loaded
 */
GeodisMap.prototype.addEventMapLoaded = function(callback) {
    if (this.isLoaded) {
        callback();
    } else {
        this.mapLoadedListeners.push(callback);
    }
    return this;
};

/**
 * Add listener on map change (zoom, position)
 */
GeodisMap.prototype.onChange = function(callback) {
    this.callbackListeners.push(callback);
    return this;
};

/**
 * Call listener on map move
 */
GeodisMap.prototype.onMove = function() {
    var center = this.map.getCenter();
    var zoom = this.map.getZoom();
    var bounds = this.map.getBounds();

    if (typeof bounds != 'undefined') {
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        // Astuce : Le centre change lors du zoom
        this.callbackListeners.forEach(function(callback) {
            callback({
                latitude: center.lat(),
                longitude: center.lng(),
                minLongitude: sw.lng(),
                maxLongitude: ne.lng(),
                minLatitude: sw.lat(),
                maxLatitude: ne.lat(),
                zoom: zoom
            });
        });
    }

    return this;
};

/**
 * Init listener on google map
 */
GeodisMap.prototype.listen = function() {
    google.maps.event.addListener(this.map, 'center_changed', (function() {
        this.onMove();
    }).bind(this));

    google.maps.event.addListener(this.map, 'zoom_changed', (function() {
        this.onMove();
    }).bind(this));

    google.maps.event.addListener(this.map, 'idle', (function() {
        this.onMove();
    }).bind(this));

    return this;
};

/**
 * Add a marker
 */
GeodisMap.prototype.addMarker = function(options) {
    var icon = {};
    if (typeof(options.position) === 'undefined') {
        options.position = {
            latitude: this.map.getCenter().lat(),
            longitude: this.map.getCenter().lng()
        };
    }
    if (typeof(options.url) != 'undefined') {
        icon.url = options.url;
    }
    if (typeof(options.size) != 'undefined') {
        icon.size = new google.maps.Size(options.size.width, options.size.height);
    }
    if (typeof(options.iconPosition) != 'undefined') {
        icon.origin = new google.maps.Point(options.iconPosition.left * options.size.width, options.iconPosition.top * options.size.height);
    }
    if (typeof(options.anchor) != 'undefined') {
        icon.anchor = new google.maps.Point(options.anchor.left, options.anchor.top);
    }
    if (typeof(options.scaledSize) != 'undefined') {
        icon.scaledSize = new google.maps.Size(options.scaledSize.width, options.scaledSize.height);
    }
    if (typeof icon.url === 'undefined') {
        icon = null;
    }
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(options.position.latitude, options.position.longitude),
        map: this.map,
        icon: icon,
        id: options.id
    });
    if (typeof(options.popin) != 'undefined') {
        var infowindow = new google.maps.InfoWindow({
            content: options.popin
        });
        google.maps.event.addListener(marker, 'click', (function() {
            if (this.map.getZoom() >= 15) {
                infowindow.open(this.map, marker);
            } else {
                this.setCenter(marker.getPosition().lat(), marker.getPosition().lng());
                this.map.setZoom(15);
                google.maps.event.trigger(this.map, 'center_changed');
            }
        }).bind(this));
    } else if (typeof(options.onClick) != 'undefined') {
        google.maps.event.addListener(marker, 'click', function() {
            options.onClick(this.id);
        });
    }
    this.markers.push(marker);
    return this;
};

/**
 * Flush all markers
 */
GeodisMap.prototype.flushMarkers = function() {
    var marker;
    while (marker = this.markers.pop()) {
        marker.setMap(null);
    }
    return this;
};

/**
 * Get postion from address
 */
GeodisMap.prototype.getPositionFromAddress = function(address, callback) {
    if (typeof(this.geocoder) === 'undefined') {
        return false;
    }

    this.geocoder.geocode({address: address, componentRestrictions: {country: this.countryCode}}, (function(results, status) {
        if (!results.length) {
            return;
        }

        var returnGeometry = results[0].geometry;

        if (status != google.maps.GeocoderStatus.OK) {
            return;
        }

        if (typeof(callback) === 'function') {
            callback({
                latitude: returnGeometry.location.lat(),
                longitude: returnGeometry.location.lng(),
            });
        }
    }).bind(this));
};

/**
 * Fit maps to positions
 */
GeodisMap.prototype.fitPositions = function(positions) {
    var bounds = new google.maps.LatLngBounds();

    positions.forEach((function(bounds, position) {

        var loc = new google.maps.LatLng(position.latitude, position.longitude);
        bounds.extend(loc);
    }).bind(this, bounds));

    this.map.fitBounds(bounds);
}

/**
 * Set Address
 */
GeodisMap.prototype.setAddress = function(address, callback) {
    if (typeof(this.geocoder) === 'undefined') {
        return false;
    }

    this.geocoder.geocode({address: address, componentRestrictions: {country: this.countryCode}}, (function(results, status) {
        if (!results.length) {
            return;
        }

        var returnGeometry = results[0].geometry;

        if (status != google.maps.GeocoderStatus.OK) {
            return;
        }
        this.map.fitBounds(returnGeometry.viewport);

        google.maps.event.trigger(this.map, 'center_changed'); // Force center change

        if (typeof(callback) === 'function') {
            callback({
                latitude: returnGeometry.location.lat(),
                longitude: returnGeometry.location.lng(),
            });
        }
    }).bind(this));
};

/**
 * Add map legend
 */
GeodisMap.prototype.addLegend = function(options) {
    this.map.controls[options.position].push(document.getElementById(options.area));
};


/**
 * Update marker
 */
GeodisMap.prototype.updateMarker = function(id, imgOff, imgOn) {
    if (typeof imgOn == 'undefined' || imgOn == '') {
        imgOn = 'images/picto_map_active.png';
    }

    this.markers.forEach((function(marker, index) {
        if (marker.id == id) {
            marker.icon.url = imgOn;
            marker.icon.origin.y = 0;
        }
        this.markers[index].setMap(this.map);
    }).bind(this));
};


