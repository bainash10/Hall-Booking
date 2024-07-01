# Hall Booking System

This repository contains a web-based hall booking system developed using PHP and MySQL. Users can book halls, manage bookings, and view details of their bookings based on their roles.

## Project Structure

- **`config.php`**: Configuration file for database connection.
- **`book_hall.php`**: PHP script to allow HODs and exam section staff to book halls.
- **`view_bookings.php`**: Displays bookings made by users, allowing actions like viewing, editing, and deleting bookings.
- **`delete_booking.php`**: Allows users with appropriate permissions (HODs and Principal) to delete bookings.
- **`view_letter.php`**: Displays the uploaded letter (PDF) associated with a booking.
- **`uploads/letters/`**: Directory to store uploaded PDF files for booking letters.

## Installation

To deploy this application locally or on a server, follow these steps:

1. **Clone the repository**:
   ```sh
   git clone https://github.com/yourusername/hall-booking-system.git
   cd hall-booking-system


2. **Database Setup**:
- Import the `database.sql` file into your MySQL database.
- Update `config.php` with your database credentials.

3. **File Permissions**:
- Ensure `uploads/letters/` directory has write permissions for storing PDF files.

## Usage

1. **Book Hall (`book_hall.php`)**:
- Select a hall, provide event details, and upload a PDF letter.
- Validates date and time to prevent overlaps with existing bookings.
- Only accessible to HODs and exam section staff.

2. **View Bookings (`view_bookings.php`)**:
- Lists all bookings made by the logged-in user.
- Allows editing and deleting bookings if permissions allow.

3. **Delete Booking (`delete_booking.php`)**:
- Deletes a booking if the logged-in user is the requester or a Principal.

4. **View Letter (`view_letter.php`)**:
- Displays the uploaded PDF letter associated with a booking.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

---

Developed by [Nischal Baidar](https://github.com/bainash10)
