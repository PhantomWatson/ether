const registration = {
    currentlyRequesting: null,
    colorNameRequest: null,
    abortController: null,

    init: async function () {
        this.abortController = new AbortController();
        const colorHex = document.getElementById('color_hex');
        new jscolor.color(colorHex, {hash: false});

        colorHex.addEventListener('change', async () => {
            if (this.validateColor()) {
                await this.checkColorAvailability();
                await this.getColorName();
            }
        });

        const form = document.getElementById('register');
        form.addEventListener('submit', (event) => {
            if (!this.validateColor()) {
                alert('Please select a valid color.');
                event.preventDefault();
            }
        });

        await this.getColorName();
    },

    getColor: function () {
        return document.getElementById('color_hex').value;
    },

    validateColor: function () {
        const chosenColor = this.getColor();

        // Validate color
        const pattern = new RegExp('^[0-9A-F]{6}$', 'i');
        if (!pattern.test(chosenColor)) {
            const errorMessage = 'Bad news. ' + chosenColor + ' is not a valid hexadecimal color.';
            this.showColorFeedback(errorMessage, 'error');
            return false;
        }

        return true;
    },

    showColorFeedback: function (message, className) {
        const msgContainer = document.querySelector('#reg_color_input .evaluation_message');
        if (message === msgContainer.textContent) {
            return;
        }

        msgContainer.textContent = message;
        msgContainer.classList.remove('success');
        msgContainer.classList.remove('error');
        msgContainer.classList.add(className);
        msgContainer.style.display = 'inline';
    },

    checkColorAvailability: async function () {
        if (this.currentlyRequesting) {
            this.abortController.abort();
        }

        this.currentlyRequesting = true;
        const color = this.getColor();
        const message = 'Checking to see if that color is available...';
        this.showColorFeedback(message, null);
        try {
            let response = await fetch('/users/checkColorAvailability/' + color, {
                signal: this.abortController.signal,
            });
            const data = await response.json();
            if (!data.hasOwnProperty('available') || typeof(data.available) !== 'boolean') {
                this.showColorFeedback('There was an error checking the availability of that color.', null);
            } else if (data.available === true) {
                this.showColorFeedback('This color is available! :)', 'success');
            } else if (data.available === false) {
                this.showColorFeedback('This color is already taken. :(', 'error');
            }
            this.currentlyRequesting = false;
        } catch (err) {
            this.currentlyRequesting = false;
            if (err.name === 'AbortError') {
                // Do nothing
                return;
            }
            this.showColorFeedback('There was an error checking the availability of that color.', null);
            throw err;
        }
    },

    getColorName: async function () {
        if (this.colorNameRequest !== null) {
            this.colorNameRequest.abort();
        }
        const colorLabel = document.querySelector('#reg_color_input label');
        colorLabel.innerHTML = 'Color: <img src="/img/loading_small.gif" class="loading" alt="Loading..." />';
        try {
            let response = await fetch('/colors/name/' + this.getColor());
            let data = await response.json();
            console.log(data);
            if (!data.hasOwnProperty('name')) {
                colorLabel.innerHTML = 'Color';
                console.log('Error retrieving color name');
            } else {
                colorLabel.innerHTML = 'Color: "' + data.name + '"';
            }
        } catch (error) {
            colorLabel.innerHTML = 'Color';
            console.log('Error retrieving color name');
        }
    }
};
