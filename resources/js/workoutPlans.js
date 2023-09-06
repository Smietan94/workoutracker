import { Modal }          from "bootstrap";
import { get, post, del } from "./ajax";
import DataTable          from "datatables.net";

window.addEventListener('DOMContentLoaded', function() {
    let setCounter  = 1
    let trainingDay = 0
    let trainingsPerWeek

    const newWorkoutPlanModal  = new Modal(document.getElementById('newWorkoutPlanModal'))
    const editWorkoutPlanModal = new Modal(document.getElementById('editWorkoutPlanModal')) 
    const addExerciseModal     = new Modal(document.getElementById('addExerciseModal'))

    document.querySelector('.new-workout-plan-btn').addEventListener('click', function(event) {
        openNewWorkoutPlanModal(newWorkoutPlanModal)
    })

    document.querySelector('.save-new-workout-plan-btn').addEventListener('click', function(event) {
        trainingsPerWeek = newWorkoutPlanModal._element.querySelector('input[name="trainingsPerWeek"]').value
        post('/workoutplans', {
            name:             newWorkoutPlanModal._element.querySelector('input[name="name"]').value,
            trainingsPerWeek: trainingsPerWeek,
            notes:            newWorkoutPlanModal._element.querySelector('input[name="notes"]').value
        }, newWorkoutPlanModal._element).then(response => {
            if (response.ok) {
                table.draw()
                document.getElementById('workout-name-input').value  = ''
                document.getElementById('training-days-input').value = ''
                document.getElementById('notes-input').value         = ''
                newWorkoutPlanModal.hide()
                addExerciseModal.show()
            }
            updateTrainingDayCounterHeader(trainingDay + 1, trainingsPerWeek)
            if (trainingsPerWeek - 1 == trainingDay) {
                document.getElementById('save-training-day-btn').innerHTML = `
                    <i class="bi bi-save me-1"></i>
                    Finish
                `
                trainingDay = 0
            } else {
                document.getElementById('save-training-day-btn').innerHTML = `
                    <i class="bi bi-calendar-plus me-1"></i>
                    Next Training Day
                `
            }
        })
    })

    const table = new DataTable('#workoutPlansTable', {
        serverSide: true,
        ajax: '/workoutplans/load',
        orderMulti: false,
        columns: [
            {data: row => `
                <a href="/trainingPlan/${ row.id }">${ row.name }</a>
            `},
            {data: "notes"},
            {data: "trainingsPerWeek"},
            {data: "createdAt"},
            {data: "updatedAt"},
            {
                sortable: false,
                data: row => `
                    <div class="d-flex flex-">
                        <button class="ms-2 btn btn-outline-primary delete-workout-plan-btn"
                                data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button class="ms-2 btn btn-outline-primary edit-workout-plan-btn"
                                data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </div>
                `
            }
        ]
    })
    
    document.querySelector('#workoutPlansTable').addEventListener('click', function (event) {
        const editBtn   = event.target.closest('.edit-workout-plan-btn')
        const deleteBtn = event.target.closest('.delete-workout-plan-btn')

        if (editBtn) {
            const workoutPlanId = editBtn.getAttribute('data-id')

            get(`/workoutplans/${ workoutPlanId }`)
                .then(response => response.json())
                .then(response => openEditWorkoutPlanModal(editWorkoutPlanModal, response))
        } else {
            const workoutPlanId = deleteBtn.getAttribute('data-id')

            if (confirm("Are you sure you want to delete this workout plan?")) {
                del(`/workoutplans/${ workoutPlanId }`).then(() => {
                    table.draw()
                })
            }
        }
    })

    document.querySelector('.update-workout-plan-btn').addEventListener('click', function (event) {
        const workoutPlanId = event.currentTarget.getAttribute('data-id')

        post(`/workoutplans/${ workoutPlanId }`, {
            name:  editWorkoutPlanModal._element.querySelector('input[name="name"]').value,
            notes: editWorkoutPlanModal._element.querySelector('input[name="notes"]').value
        }, editWorkoutPlanModal._element).then(response => {
            if (response.ok) {
                table.draw()
                editWorkoutPlanModal.hide()
            }
        })
    })

    document.querySelector('.add-set-btn').addEventListener('click', function (event) {
        const setsConainer = document.getElementById('setsContainer')
        addInput(setsConainer, setCounter)
        setCounter++
    })

    document.querySelector('.add-new-exercise-btn').addEventListener('click', function(event) {
        addExercise(addExerciseModal, table, trainingDay)
        setTimeout(() => {
            addExerciseModal.show()
        }, 500);
        setCounter = 1
    })

    document.querySelector('.next-training-day-btn').addEventListener('click', function(event) {
        addExercise(addExerciseModal, table, trainingDay)
        console.log(trainingDay)
        console.log(trainingsPerWeek)
        if (trainingsPerWeek - 1 == trainingDay) {
            console.log('git')
            trainingDay++
            setTimeout(() => {
                document.getElementById('save-training-day-btn').innerHTML = `
                    <i class="bi bi-save me-1"></i>
                    Finish
                `
            }, 500)
            trainingDay = 0
        } else if (trainingsPerWeek - 2 == trainingDay) {
            console.log('git')
            trainingDay++
            setTimeout(() => {
                updateTrainingDayCounterHeader(trainingDay + 1, trainingsPerWeek)
                document.getElementById('save-training-day-btn').innerHTML = `
                    <i class="bi bi-save me-1"></i>
                    Finish
                `
            }, 500)
            setTimeout(() => {
                addExerciseModal.show();
            }, 500)
        } else if (trainingsPerWeek >= trainingDay) {
            console.log('dupa')
            trainingDay++
            setTimeout(() => {
                updateTrainingDayCounterHeader(trainingDay + 1, trainingsPerWeek)
                document.getElementById('save-training-day-btn').innerHTML = `
                    <i class="bi bi-calendar-plus me-1"></i>
                    Next Training Day
                `
            }, 500)
            setTimeout(() => {
                addExerciseModal.show();
            }, 500)
        } else {
            trainingDay = 0
            setTimeout(updateTrainingDayCounterHeader(trainingDay + 1, trainingsPerWeek), 500)
        }
        setCounter = 1
    })

    document.querySelector('.remove-set-btn').addEventListener('click', function (event) {
        let setsInputs = document.querySelectorAll('.set-input-div')

        if (setsInputs.length > 1) {
            let lastInputDiv = setsInputs[setsInputs.length - 1]
            lastInputDiv.remove()
            setCounter-- 
        } else {
            alert('Atleast one set required')
        }
    })
})

function openEditWorkoutPlanModal(modal, {id, name, notes}) {
    const nameInput  = modal._element.querySelector('input[name="name"]')
    const notesInput = modal._element.querySelector('input[name="notes"]')

    nameInput.value  = name
    notesInput.value = notes

    modal._element.querySelector('.update-workout-plan-btn').setAttribute('data-id', id)

    modal.show()
}

function openNewWorkoutPlanModal(modal) {
    modal._element.querySelector('.new-workout-plan-btn')

    modal.show()
}

function addInput(setsConainer, setCounter) {
    const newSet   = document.createElement('input')
    const inputDiv = document.createElement('div')

    inputDiv.classList.add('form-outline')
    inputDiv.classList.add('form-white')
    inputDiv.classList.add('set-input-div')
    newSet.classList.add('sets-input')
    newSet.classList.add('form-control')
    newSet.classList.add('form-control-lg')

    newSet.type        = 'number'
    newSet.name        = 'set' + (++setCounter)
    newSet.placeholder = 'Set ' + setCounter
    newSet.min         = "1"

    if (setCounter > 1) {
        newSet.classList.add('mt-4')
    }

    inputDiv.appendChild(newSet)
    setsConainer.appendChild(inputDiv)
}

function addExercise(modal, table, trainingDay) {
    const setsContainer     = document.getElementById('setsContainer')
    const sets              = setsContainer.querySelectorAll('.sets-input')
    const nameInput         = modal._element.querySelector('input[name="name"]')
    const categoryNameInput = modal._element.querySelector('input[name="categoryName"]')
    const descriptionInput  = modal._element.querySelector('input[name="description"]')
    const data              = new FormData()

    data['name']         = nameInput.value
    data['categoryName'] = categoryNameInput.value
    data['description']  = descriptionInput.value
    data['trainingDay']  = trainingDay
    console.log(data)

    for (let i = 0; i < sets.length; i++) {
        data[sets[i].name] = sets[i].value
    }

    post(`workoutplans/addexercise`, 
        data, modal._element).then(response => {
        if (response.ok) {
            table.draw()
            modal.hide()
            nameInput.value         = ''
            categoryNameInput.value = ''
            descriptionInput.value  = ''
            setsContainer.innerHTML = ''
            addInput(setsContainer, 0)
            console.log(response)
        }
    })
}

function updateTrainingDayCounterHeader(trainingDay, trainingsPerWeek) {
    const header = document.getElementById('dayCounterHeader')

    header.innerHTML = `day: ${ trainingDay } of ${ trainingsPerWeek }`
}