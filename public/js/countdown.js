(function () {
  const countdownElement = document.querySelector('#countdown')
  const hoursElement = document.querySelector('#hours')
  const minutesElement = document.querySelector('#minutes')
  const secondsElement = document.querySelector('#seconds')

  if (countdownElement) {
      const end = parseInt(countdownElement.dataset.remainingTime) * 1000
      setInterval(function () {
        const remainingTime = Math.floor((end - Date.now()) / 1000);

        const hours = Math.floor(remainingTime / 3600)
        const minutes = Math.floor((remainingTime % 3600) / 60)
        const seconds = remainingTime % 60
        hoursElement.textContent = hours < 10 ? `0${hours}` : hours
        minutesElement.textContent = minutes < 10 ? `0${minutes}` : minutes
        secondsElement.textContent = seconds < 10 ? `0${seconds}` : seconds
      }, 1000)
  }
})();
