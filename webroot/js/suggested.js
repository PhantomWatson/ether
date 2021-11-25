class SuggestedWords {
    constructor() {
        const buttons = document.querySelectorAll('#suggested-words button');
        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const clickedButton = event.target;
                document.getElementById('input-thought-word').value = clickedButton.innerHTML;
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
