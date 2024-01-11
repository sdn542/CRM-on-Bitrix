addEventListener("DOMContentLoaded", () => {
  const filterBtn = document.querySelectorAll('.filter')
  const filterBody = document.querySelectorAll('.filter-body')
  filterBtn.forEach(btn => {
    btn.addEventListener('click', (event) => {
      event._noClose = true
      btn.querySelector('.filter-body').classList.add('hidden')
    })
  })

  document.body.addEventListener('click', (event) => {
    if (!event._noClose) {
      filterBody.forEach(item => {
        item.classList.remove('hidden')
      })
    }
  })
})