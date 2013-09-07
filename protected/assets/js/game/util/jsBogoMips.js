/**
 * jsBogoMips
 *
 * The term BogoMips and the calculation thereof comes from Linux, and more particularly its author Linus.
 * Linus needed a calibration value in the kernel to deal with processor speeds. Bogo comes from bogus
 * and MIPS stands for Millions of Instructions Per Second. BogoMips is essentially the number of million
 * times per second a processor can do absolutely nothing. It is a portable way to get an indication of
 * the CPU speed.
 *
 * In JavaScript, there is no way to get the CPU speed of the client environment, so this function tries
 * to do the same things as BogoMips in the Linux kernel, except that instead of determining the BogoMips
 * of the CPU itself, it more determines the BogoMips of the JavaScript engine in the browser, making this
 * even more bogus than BogoMips. Unlike the Linux BogoMips value which is quantizised so you're most likely
 * to get the exact same number all the time, the JS-BogoMips value can vary on each run due to system load
 * and other underlying factors.
 *
 * author: Steven Pothoven
 * 
 */

jsBogoMips = function() {
    // performance matrix defines threshold levels to allow various
    // functionality
    // Format:
    //     browserName : [ level0, level1, level2]
    // where:
    //     level0 = no effects
    //     level1 = minimal effects
    //     level2 = normal effects
    //     level3 = not specified, but anything over level3 gives full effects
    var performanceMatrix = {
        Firefox   : [0.05, 0.10, 0.15],
        IE        : [0.05, 0.10, 0.15],
        Safari    : [0.05, 0.10, 0.20],
        Opera     : [0.20, 0.30, 0.40]
    }
    var calculatedBogoMips = null;
    var clientEffectsLevel = null;


    /**
     * calculateJsBogoMips
     *
     * determine the jsBogoMips value for this client
     * 
     * @return {Number} jsBogoMips value
     */
	function calculateJsBogoMips() {
    	// get the initial time
    	var startTime = (new Date()).getTime();

    	// initialize the loop counter.  This is done by adding 0 + 0 to try to
    	// simulate the time taken to accumulate the total in the loop
    	var loops_per_sec = 0 + 0;

    	// In an attempt to compensate for the time it takes to retrieve the time and the time it
    	// takes to increment the counter, we calculate the difference between the first time we
    	// got the time, and the second time
    	var currentTime = (new Date()).getTime();
    	var compensation = currentTime - startTime;
	
	    // once more get the current time (in milliseconds) and add 1000 to it to determine the end time
	    var endTime = (new Date()).getTime() + 1000;
	
	    while (currentTime < endTime) {
        	loops_per_sec++;
        	currentTime = (new Date()).getTime();
    	}

    	calculatedBogoMips = (loops_per_sec + (loops_per_sec * compensation)) / 1000000;
    	return calculatedBogoMips;
	}

    /**
     * resetCalculations
     *
     * To optimize the calculations, these functions work like singletons and simply return the last
     * value if already calculated.  However, since it's possible that the original caluclation was
     * incorrect (due to system load, etc.), this function is placed in a timed interval to preiodically
     * reset the calculations in order to be more accurate if the system load changes.
     */
    function resetCalculations() {
        calculatedBogoMips = null;
        clientEffectsLevel = null;
        calculateJsBogoMips();
    }
    
    return {
        /**
         * getJsBogoMips
         * Return the current JSBogoMips value for this client
         *
         * @return {Number} the number of million times per second the client can loop through an empty loop
         * (this is usually a number less than 1 which would mean 1 million times through the loop)
         */
        getJsBogoMips : function() {
            return calculatedBogoMips || calculateJsBogoMips();
        },

        /**
         * getAveragedJsBogoMips
         * Return the averaged JSBogoMips value for this client.  
         *
         * @param  {Number} sampleNumber - the number of JsBogoMips values to calculate and average together
         * @return {Number} the number of million times per second the client can loop through an empty loop
         * (this is usually a number less than 1 which would mean 1 million times through the loop)
         */
		getAveragedJsBogoMips : function(sampleNumber) {
			if (!sampleNumber) {
				sampleNumber = 3;
			}
    		var samples = [];
    		var total = 0;
    		for (var i = 0; i < sampleNumber; i++) {
        		samples.push(calculateJsBogoMips());
        		total += samples[i];
    		}
    		// since all JS math is floating point, convert the values back to integers for the averaging so
    		// we don't get a bunch of meaningless extra values
    		var average = Math.round(Math.round(total * 1000000) / sampleNumber) / 1000000;
    		return {samples: samples, average: average};
		},

        /**
         * getEffectsLevel
         *
         * return the effects level for the current client
         * In an application, this is probably the only function you'd really want to use to check the effects
         * level before invoking some fancy special effect (see script.aculo.us).  Because it utilizes the singleton
         * value, it doesn't incur calculation costs each time you check and it periodically updates the jsBogoMips
         * calculation in case you CPU workload has changed.
         *
         * @return {Number} value from 0 to 4 defining client capabilities
         */
        getEffectsLevel : function() {
            if (clientEffectsLevel === null) {
                var jsBogoMips = this.getJsBogoMips();
                clientEffectsLevel = 0;
                var browser;

                browserTest : {
                    if(navigator.userAgent.indexOf("Firefox")!=-1){
                       browser = 'Firefox';
                    } else if (Prototype.Browser.IE) {
                        browser = 'IE';
                    } else if (Prototype.Browser.Opera) {
                        browser = 'Opera';
                    } else if (Prototype.Browser.WebKit) {
                        browser = 'Safari';
                    } else {
                        // Unknown browser - no effects or charting enabled
                       break browserTest;
                    }

                    for (clientEffectsLevel = 0; clientEffectsLevel < 3; clientEffectsLevel++) {
                        if (jsBogoMips <= performanceMatrix[browser][clientEffectsLevel]) {
                            break browserTest;
                        }
                    }
                    clientEffectsLevel = 3;
                }

                // periodically re-calculate the level (set to every 10 minutes)
                setInterval(resetCalculations, 600000);
            }
            return clientEffectsLevel;
        }
    }
}();
