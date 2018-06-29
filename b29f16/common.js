function validateEmail (str) {
  var re = /[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/
  return re.test(str)
}

function validatePassword (str) {
  var lower = /[a-z]+/
  var upper = /[A-Z]+/
  var digit = /[0-9]+/
  return lower.test(str) && (upper.test(str) || digit.test(str))
}

$(document).ready(function () {
  // check for https
  if (window.location.protocol !== 'https:')
    window.location.href = 'https:' + window.location.href.substring(window.location.protocol.length)

  // check that cookies are enabled
  if (!navigator.cookieEnabled)  {
    window.location.href = 'cookieJsDisabled.php'
  }

  // handle login
  $("form.login").submit(function (event) {
    var email = document.getElementById('emailLogin').value
    var password = document.getElementById('pswLogin').value

    if (!validateEmail(email)) {
      event.preventDefault()
      alert("Email format is not correct!");
      return
    }

    if (!validatePassword(password)) {
      event.preventDefault()
      alert("Password format is not correct!");
      return
    }
  })

  // handle signup
  $("form.signup").submit(function (event) {
    var email = document.getElementById('emailSignup').value
    var password = document.getElementById('pswSignup').value

    if (!validateEmail(email)) {
      event.preventDefault()
      alert("Email format is not correct!");
      return
    }

    if (!validatePassword(password)) {
      event.preventDefault()
      alert("Password format is not correct!");
      return
    }
  })
})