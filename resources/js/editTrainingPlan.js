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
    const newSetAttributes  = {
        'placeholder': 'Reps',
        'type'       : 'number',
        'min'        : '0',
        'name'       : `trainingDays[${ trainingDayIndex }][exercises][${ exerciseIndex}][sets][set${ setsCount }]`
    }

    addClasses(inputDiv, ['set-input-div'])
    addClasses(newSet, ['form-control', 'mt-1', 'sets-input'])
    addAttributes(newSet, newSetAttributes)

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
    let basicInputClassArray  = ['form-control', 'mt-1']
    let basicButtonClassArray = ['btn', 'btn-dark', 'add-set-btn', 'mt-1']
    const trainingDayIndex    = addExerciseButton.getAttribute('data-training-day-index')
    const tableBody           = document.getElementById(`trainingDayTBody${ trainingDayIndex }`)
    const exercisesNum        = tableBody.querySelectorAll('tr').length
    const prefix              = `trainingDays[${ trainingDayIndex }][exercises][${ exercisesNum }]`
    const newTableRow         = document.createElement('tr')

    newTableRow.classList.add('exercise-row')
    newTableRow.setAttribute('data-exercise-id', `exercise${ trainingDayIndex }${ exercisesNum }`)

    const indexTD            = document.createElement('td')
    const nameInputTD        = document.createElement('td')
    const descriptionInputTD = document.createElement('td')
    const setsInputTD        = document.createElement('td')
    const categoryInputTD    = document.createElement('td')
    const actionTD           = document.createElement('td')

    actionTD.classList.add('text-center')
    indexTD.innerHTML     = exercisesNum + 1
    indexTD.id            = 'indexCell'
    nameInputTD.id        = 'nameCell'
    descriptionInputTD.id = 'descriptionCell'
    setsInputTD.id        = 'setsCell'
    categoryInputTD.id    = 'categoryCell'
    actionTD.id           = 'actionCell'

    const idHiddenInput           = document.createElement('input')
    const idHiddenInputAttributes = {
        'id': 'exerciseId',
        'type': 'hidden',
        'name': prefix + '[exerciseId]',
        'value': null
    }
    addAttributes(idHiddenInput, idHiddenInputAttributes)

    const nameInput           = document.createElement('input')
    const nameInputAttributes = {
        'name'       : prefix + '[exerciseName]',
        'type'       : 'text',
        'placeholder': 'Exercise Name'
    }
    addAttributes(nameInput, nameInputAttributes)
    addClasses(nameInput, basicInputClassArray)

    const descriptionInput           = document.createElement('input')
    const descriptionInputAttributes = {
        'name'       : prefix + '[description]',
        'type'       : 'text',
        'placeholder': 'Description'
    }
    addAttributes(descriptionInput, descriptionInputAttributes)
    addClasses(descriptionInput, basicInputClassArray)

    const setInput           = document.createElement('input')
    const setInputAttributes = {
        'name'       : prefix + '[sets][set0]',
        'type'       : 'number',
        'min'        : '0',
        'placeholder': 'Reps'
    }
    addAttributes(setInput, setInputAttributes)
    addClasses(setInput, basicInputClassArray.concat(['sets-input']))

    const categoryInput           = document.createElement('input')
    const categoryInputAttributes = {
        'name'       : prefix + '[category]',
        'type'       : 'text',
        'placeholder': 'Category Name'
    }
    addAttributes(categoryInput, categoryInputAttributes)
    addClasses(categoryInput, basicInputClassArray)

    const setsContainer = document.createElement('div')
    setsContainer.id    = `setsContainer${ trainingDayIndex }${ exercisesNum }`

    const setInputDiv = document.createElement('div')
    setInputDiv.classList.add('set-input-div')

    const setBtnDiv = document.createElement('div')
    addClasses(setBtnDiv, ['row', 'mt-1'])

    const addBtnDiv = document.createElement('div')
    addBtnDiv.classList.add('col')

    const rmBtnDiv = document.createElement('div')
    rmBtnDiv.classList.add('col')

    const addSetButton           = document.createElement('button')
    const addSetButtonAttributes = {
        'type'                   : 'button',
        'value'                  : `${ trainingDayIndex }${ exercisesNum }`,
        'data-training-day-index': trainingDayIndex,
        'data-exercise-index'    : exercisesNum
    }
    addAttributes(addSetButton, addSetButtonAttributes)
    addClasses(addSetButton, basicButtonClassArray.concat(['w-100']))

    const removeSetButton = document.createElement('button')
    removeSetButton.type  = 'button'
    removeSetButton.value = `${ trainingDayIndex }${ exercisesNum }`
    addClasses(removeSetButton, basicButtonClassArray.concat(['w-100']))

    const removeExerciseButton = document.createElement('button')
    removeExerciseButton.type  = 'button'
    removeExerciseButton.value = `${ trainingDayIndex }${ exercisesNum }`
    addClasses(removeExerciseButton, basicButtonClassArray)

    const addIcon = document.createElement('i')
    addClasses(addIcon, ['bi', 'bi-plus-circle', 'me-1'])

    const dashIcon = document.createElement('i')
    addClasses(dashIcon, ['bi', 'bi-dash-circle', 'me-1'])

    const trashIcon = document.createElement('i')
    addClasses(trashIcon, ['bi', 'bi-trash3-fill'])

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

function addAttributes(element, attributesArray) {
    for (const [key, value] of Object.entries(attributesArray)) {
        element.setAttribute(key, value)
    }
}

function addClasses(element, classArray) {
    for (let i = 0; i < classArray.length; i++) {
        element.classList.add(classArray[i])
    }
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
        setsContainer.id    = `setsContainer${ trainingDayIndex }${ exerciseIndex }`

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