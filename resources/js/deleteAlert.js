import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'
export default function deleteAlert() {
    return Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this!",
        icon: 'warning',
        showCancelButton: true,
        width: 400,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    })
}
