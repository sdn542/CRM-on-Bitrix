addEventListener("DOMContentLoaded", () => {
  
  const stepTabs = document.querySelectorAll('[data-step-tab]')  

  const changeStep = (stepId) => {
    const stepBlocks = document.querySelectorAll('.block[data-step]')

    // Показываем активынй шаг формы
    stepBlocks.forEach(block => {
      block.getAttribute('data-step') === stepId ? block.classList.add('active-step') :  block.classList.remove('active-step')
    })

    // Показываем активный таб формы
    stepTabs.forEach(tab => {
      tab.getAttribute('data-step-tab') === stepId ? tab.classList.add('active-tab') :  tab.classList.remove('active-tab')
    })
  }

  const nextStepButtons = document.querySelectorAll('[data-next-step]')
  nextStepButtons.forEach(btn => {
    btn.addEventListener('click', (event) => {
      const step = event.target.getAttribute('data-next-step')
      console.log(step)
      changeStep(step)
      if (step === 'step-creditor') {
        document.querySelector('.form-button').classList.add('hidden')
      }
    })
  })

  stepTabs.forEach(tab => {
    tab.addEventListener('click', (event) => {
      changeStep(event.target.getAttribute('data-step-tab'))
    })
  })

  changeStep('step-agreement')
})