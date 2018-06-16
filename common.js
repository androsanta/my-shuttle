$(document).ready(function () {
  // check for https
  if (window.location.protocol !== 'https:')
    window.location.href = 'https:' + window.location.href.substring(window.location.protocol.length)

  // handle login
  $("form.login").submit(function (event) {
    var email = document.getElementById('email').value
    var password = document.getElementById('psw').value
    // var reg = //
    // event.preventDefault()
  })

  // handle
  $("form.signup").submit(function (event) {
    console.log('dio bonooo')
    event.preventDefault()
  })
})