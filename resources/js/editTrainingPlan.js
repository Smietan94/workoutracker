import { MODAL }          from "bootstrap"
import { get, post, del } from "./ajax"
import DataTable          from "datatables.net"

window.addEventListener('DOMContentLoaded', function () {
    const openTrainingDayButtons = document.querySelectorAll('.training-day-btn')
    const addExerciseButtons     = document.querySelectorAll('.add-exercise-btn')
    const removeExerciseButtons  = Array.from(document.querySelectorAll('.remove-exercise-btn'))
    const addSetButtons          = Array.from(document.querySelectorAll('.add-set-btn'))
    const removeSetButtons       = Array.from(document.querySelectorAll('.remove-set-btn'))

    processButtons(openTrainingDayButtons, openTrainingDay)
    processButtons(addExerciseButtons, addExerciseTableRow)
    processButtons(removeExerciseButtons, removeExerciseRow)
    processButtons(addSetButtons, addSet)
    processButtons(removeSetButtons, removeSet)
})

function processButtons(buttons, buttonFunction) {
    buttons.forEach(function (button) {
        button.addEventListener('click', function() {
            buttonFunction(button)
        })
    })
}

function removeExerciseRow(removeExerciseButton) {
    const exerciseRowToDelete = removeExerciseButton.closest('tr[data-exercise-id]')
    const tbody               = exerciseRowToDelete.closest('tbody')

    if (exerciseRowToDelete) {
        exerciseRowToDelete.remove()
        updateTbody(tbody)
    } else {
        alert('Exercise row not found')
    }
}

function openTrainingDay(opentTrainingDayButton) {
    const tableId = opentTrainingDayButton.getAttribute('data-target')
    const table   = document.getElementById(tableId)

    if (table.classList.contains('table-hidden')) {
        table.classList.remove('table-hidden')
    } else {
        table.classList.add('table-hidden')
    }
}

function addSet(addSetButton) {
    const exerciseGridIndex = addSetButton.value
    const trainingDayIndex  = addSetButton.getAttribute('data-training-day-index')
    const exerciseIndex     = addSetButton.getAttribute('data-exercise-index')
    const newSet            = document.createElement('input')
    const inputDiv          = document.createElement('div')
    const setsContainer     = document.getElementById(`setsContainer${ exerciseGridIndex }`)
    const setsCount         = setsContainer.querySelectorAll('.sets-input').length

    inputDiv.classList.add('set-input-div')
    newSet.classList.add('sets-input')
    addBasicInputClasses(newSet)
    newSet.placeholder = 'Reps'
    newSet.type        = 'number'
    newSet.min         = '0'
    newSet.name        = `trainingDays[${ trainingDayIndex }][exercises][${ exerciseIndex}][sets][set${ setsCount }]`

    inputDiv.appendChild(newSet)
    setsContainer.appendChild(inputDiv)
}

function removeSet(removeSetButton) {
    const exerciseGridIndex    = removeSetButton.value
    const setsContainer = document.getElementById(`setsContainer${ exerciseGridIndex }`)
    const setsInputs    = setsContainer.querySelectorAll('.set-input-div')
    
    if (setsInputs.length > 1) {
        let lastInputDiv = setsInputs[setsInputs.length - 1]
        lastInputDiv.remove()
    } else {
        alert('Atleast one set required')
    }
}

function addExerciseTableRow(addExerciseButton) {
    const trainingDayIndex = addExerciseButton.getAttribute('data-training-day-index')

    const tableBody    = document.getElementById(`trainingDayTBody${ trainingDayIndex }`)

    const exercisesNum = tableBody.querySelectorAll('tr').length
    const prefix       = `trainingDays[${ trainingDayIndex }][exercises][${ exercisesNum }]`

    const newTableRow  = document.createElement('tr')
    newTableRow.classList.add('exercise-row')
    newTableRow.setAttribute('data-exercise-id', `exercise${ trainingDayIndex }${ exercisesNum }`)

    const indexTD = document.createElement('td')
    indexTD.innerHTML = exercisesNum + 1
    const nameInputTD        = document.createElement('td')
    const descriptionInputTD = document.createElement('td')
    const setsInputTD        = document.createElement('td')
    const categoryInputTD    = document.createElement('td')
    const actionTD           = document.createElement('td')
    actionTD.classList.add('text-center')

    indexTD.id            = 'indexCell'
    nameInputTD.id        = 'nameCell'
    descriptionInputTD.id = 'descriptionCell'
    setsInputTD.id        = 'setsCell'
    categoryInputTD.id    = 'categoryCell'
    actionTD.id           = 'actionCell'

    const nameInput       = document.createElement('input')
    nameInput.name        = prefix + '[exerciseName]'
    nameInput.type        = 'text'
    nameInput.placeholder = 'Exercise Name'
    addBasicInputClasses(nameInput)

    const descriptionInput       = document.createElement('input')
    descriptionInput.name        = prefix + '[description]'
    descriptionInput.type        = 'text'
    descriptionInput.placeholder = 'Description'
    addBasicInputClasses(descriptionInput)

    const setsContainer = document.createElement('div')
    setsContainer.id    = `setsContainer${ trainingDayIndex }${ exercisesNum }`

    const setInputDiv = document.createElement('div')
    setInputDiv.classList.add('set-input-div')

    const setBtnDiv = document.createElement('div')
    setBtnDiv.classList.add('row')
    setBtnDiv.classList.add('mt-1')

    const addBtnDiv = document.createElement('div')
    addBtnDiv.classList.add('col')

    const rmBtnDiv = document.createElement('div')
    rmBtnDiv.classList.add('col')

    const setInput       = document.createElement('input')
    setInput.name        = prefix + '[sets][set0]'
    setInput.type        = 'number'
    setInput.min         = '0'
    setInput.placeholder = 'Reps'
    setInput.classList.add('sets-input')
    addBasicInputClasses(setInput)

    const categoryInput       = document.createElement('input')
    categoryInput.name        = prefix + '[category]'
    categoryInput.type        = 'text'
    categoryInput.placeholder = 'Category Name'
    addBasicInputClasses(categoryInput)

    const addSetButton = document.createElement('button')
    addSetButton.type  = "button"
    addSetButton.value = `${ trainingDayIndex }${ exercisesNum }`
    addSetButton.classList.add('w-100')
    addBasicButtonClasses(addSetButton)
    addSetButton.setAttribute('data-training-day-index', trainingDayIndex)
    addSetButton.setAttribute('data-exercise-index', exercisesNum)

    const removeSetButton = document.createElement('button')
    removeSetButton.type  = 'button'
    removeSetButton.value = `${ trainingDayIndex }${ exercisesNum }`
    removeSetButton.classList.add('w-100')
    addBasicButtonClasses(removeSetButton)

    const removeExerciseButton = document.createElement('button')
    removeExerciseButton.type  = 'button'
    removeExerciseButton.value = `${ trainingDayIndex }${ exercisesNum }`
    addBasicButtonClasses(removeExerciseButton)

    const addIcon = document.createElement('i')
    addIcon.classList.add('bi')
    addIcon.classList.add('bi-plus-circle')
    addIcon.classList.add('me-1')

    const dashIcon = document.createElement('i')
    dashIcon.classList.add('bi')
    dashIcon.classList.add('bi-dash-circle')
    dashIcon.classList.add('me-1')

    const trashIcon = document.createElement('i')
    trashIcon.classList.add('bi')
    trashIcon.classList.add('bi-trash3-fill')

    addSetButton.appendChild(addIcon)
    removeSetButton.appendChild(dashIcon)
    removeExerciseButton.appendChild(trashIcon)

    setInputDiv.appendChild(setInput)
    setsContainer.appendChild(setInputDiv)
    addBtnDiv.appendChild(addSetButton)
    rmBtnDiv.appendChild(removeSetButton)
    setBtnDiv.appendChild(addBtnDiv)
    setBtnDiv.appendChild(rmBtnDiv)

    nameInputTD.appendChild(nameInput)
    descriptionInputTD.appendChild(descriptionInput)
    setsInputTD.appendChild(setsContainer)
    setsInputTD.appendChild(setBtnDiv)
    categoryInputTD.appendChild(categoryInput)
    actionTD.appendChild(removeExerciseButton)

    const tdArray = [indexTD, nameInputTD, descriptionInputTD, setsInputTD, categoryInputTD, actionTD]

    tdArray.forEach(function (td) {
        newTableRow.appendChild(td)
    })

    tableBody.appendChild(newTableRow)

    processButtons([addSetButton], addSet)
    processButtons([removeSetButton], removeSet)
    processButtons([removeExerciseButton], removeExerciseRow)
}

function addBasicInputClasses(input) {
    input.classList.add('form-control')
    input.classList.add('mt-1')
}

function addBasicButtonClasses(button) {
    button.classList.add('btn')
    button.classList.add('btn-dark')
    button.classList.add('add-set-btn')
    button.classList.add('mt-1')
}

function updateTbody(tbody) {
    const exercises = Array.from(tbody.querySelectorAll('.exercise-row'))
    const trainingDayIndex = tbody.getAttribute('data-training-day-index')

    for (let exerciseIndex = 0; exerciseIndex < exercises.length; exerciseIndex++) {
        const prefix           = `trainingDays[${ trainingDayIndex }][exercises][${ exerciseIndex }]`
        const exerciseRow      = exercises[exerciseIndex]
        const exerciseIdInput  = exerciseRow.querySelector('#exerciseId')
        const indexCell        = exerciseRow.querySelector('#indexCell')
        const nameInput        = getInput(exerciseRow, '#nameCell')
        const descriptionInput = getInput(exerciseRow, '#descriptionCell')
        const categoryInput    = getInput(exerciseRow, '#categoryCell')
        const setsContainer    = exerciseRow.querySelector('#setsCell').querySelector('.sets-container')
        const addSetButton     = exerciseRow.querySelector('.add-set-btn')
        const removeSetButton  = exerciseRow.querySelector('.remove-set-btn')

        indexCell.innerHTML = exerciseIndex + 1

        setsContainer.id = `setsContainer${ trainingDayIndex }${ exerciseIndex }`
        if (exerciseIdInput) {
            exerciseIdInput.name = `${ prefix }[exerciseId]`
        }
        nameInput.name        = `${ prefix }[exerciseName]`
        descriptionInput.name = `${ prefix }[description]`
        categoryInput.name    = `${ prefix }[category]`

        updateSets(prefix, setsContainer.querySelectorAll('.sets-input'))

        exerciseRow.setAttribute('data-exercise-id', `exercise${trainingDayIndex}${exerciseIndex}`)
        addSetButton.setAttribute('data-training-day-index', trainingDayIndex)
        addSetButton.setAttribute('data-exercise-index', exerciseIndex)
        addSetButton.value    = `${ trainingDayIndex }${ exerciseIndex }`
        removeSetButton.value = `${ trainingDayIndex }${ exerciseIndex }`
    }
}

function updateSets(prefix, setsInputs) {
    for (let setIndex = 0; setIndex < setsInputs.length; setIndex++) {
        setsInputs[setIndex].name = `${ prefix }[sets][set${ setIndex }]`
    }
}

function getInput(exerciseRow, id) {
    return exerciseRow.querySelector(id).querySelector('input')
}