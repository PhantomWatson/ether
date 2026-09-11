class SuggestedWords {
    constructor() {
        const buttons = document.querySelectorAll('#suggested-words button');
        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const clickedButton = event.target;
                const wordInput = document.getElementById('input-thought-word');
                wordInput.value = clickedButton.innerHTML;

                // Have a clicked word trigger the "thoughtword check" process
                wordInput.dispatchEvent(new Event('input', { bubbles: true }));

                document.getElementById('input-thought-body').focus();

                const selectedButtons = document.querySelectorAll('#suggested-words button.selected');
                selectedButtons.forEach((selectedButton) => {
                    selectedButton.classList.remove('selected')
                });

                clickedButton.classList.add('selected');
            });
        });
    }
}
