addEventListener("DOMContentLoaded", () => {
  // Показать/скрыть кнопку "Выйти"
  document.querySelector('.js-user').addEventListener('click', () => {
    document.querySelector('.logout').classList.toggle('visible')
  })

  const phoneInput = document.querySelectorAll('input[type=tel]')
   
  phoneInput.forEach((input) => {
    Inputmask({
      placeholder: "",
      mask:"+7 999 999 99 99", 
      showMaskOnHover: false, 
    }).mask(input);

  })
});