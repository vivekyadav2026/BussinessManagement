<script>
document.addEventListener('DOMContentLoaded', function() {
    function markRequiredLabels() {
        const requiredElements = document.querySelectorAll('input[required], select[required], textarea[required]');
        requiredElements.forEach(el => {
            let label = null;
            if (el.id) {
                label = document.querySelector('label[for="' + el.id + '"]');
            }
            if (!label) {
                label = el.closest('label');
            }
            if (!label && el.parentElement) {
                label = el.parentElement.querySelector('label');
                if (!label && el.parentElement.parentElement) {
                    label = el.parentElement.parentElement.querySelector('label');
                }
            }
            if (label && !label.dataset.asteriskAdded) {
                if (!label.innerHTML.includes('*')) {
                    // Rose-500 from tailwind, or standard red
                    label.innerHTML += ' <span style="color: #f43f5e; font-weight: bold; margin-left: 2px;">*</span>';
                }
                label.dataset.asteriskAdded = 'true';
            }
        });
    }

    markRequiredLabels();

    const observer = new MutationObserver((mutations) => {
        let shouldRun = false;
        for (let m of mutations) {
            if (m.addedNodes.length > 0) {
                shouldRun = true;
                break;
            }
        }
        if (shouldRun) markRequiredLabels();
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
</script>
