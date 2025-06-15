```javascript
// Assuming this is part of a larger script where the document is ready

window.addEventListener('DOMContentLoaded', function() {
    // Only set focus if body is focused (nothing else yet)
    if (document.activeElement === document.body) {
        var loginInput = document.getElementById('login-username');
        var registerInput = document.getElementById('register-username');
        if (loginInput) loginInput.focus();
        else if (registerInput) registerInput.focus();
    }
});

// ...existing code...
```