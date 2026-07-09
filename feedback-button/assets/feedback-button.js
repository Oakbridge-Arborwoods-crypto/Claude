(function () {
	var toggle = document.getElementById('feedback-toggle');
	var formWrap = document.getElementById('feedback-form-wrap');
	var form = document.getElementById('feedback-form');
	var status = document.getElementById('feedback-status');

	if (!toggle || !form) return;

	toggle.addEventListener('click', function () {
		var expanded = toggle.getAttribute('aria-expanded') === 'true';
		toggle.setAttribute('aria-expanded', String(!expanded));
		formWrap.hidden = expanded;
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var message = form.querySelector('textarea').value.trim();
		if (!message) {
			status.textContent = 'Please enter your feedback before submitting.';
			return;
		}
		status.textContent = 'Sending…';

		fetch(feedbackButton.restUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': feedbackButton.nonce,
			},
			body: JSON.stringify({ message: message }),
		})
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data.success) {
					status.textContent = data.message;
					form.reset();
				} else {
					status.textContent = 'Something went wrong. Please try again.';
				}
			})
			.catch(function () {
				status.textContent = 'Network error. Please try again.';
			});
	});
})();
