<?php

namespace App\Models;

use App\Core\Model;

final class AdminStats extends Model
{
    public function dashboard(): array
    {
        $users = $this->db->query('SELECT
            COUNT(*) AS users,
            SUM(role = "owner") AS owners,
            SUM(role = "tenant") AS tenants,
            SUM(role = "admin") AS admins,
            SUM(status = "pending") AS pending_users,
            SUM(status = "suspended") AS suspended_users
            FROM users')->fetch() ?: [];

        $properties = $this->db->query('SELECT
            SUM(status = "published") AS published_properties,
            SUM(status = "pending") AS pending_properties,
            SUM(status = "rejected") AS rejected_properties,
            SUM(status = "paused") AS paused_properties,
            SUM(status = "deleted") AS deleted_properties
            FROM properties')->fetch() ?: [];

        $bookings = $this->db->query('SELECT
            COUNT(*) AS bookings,
            SUM(status = "pending_admin") AS pending_bookings,
            SUM(status = "pending_payment") AS pending_payment_bookings,
            SUM(status = "confirmed") AS confirmed_bookings,
            SUM(status = "cancelled") AS cancelled_bookings,
            SUM(status = "completed") AS completed_bookings,
            SUM(status = "pending_payment" AND payment_status = "not_paid") AS pending_payments,
            SUM(payment_status = "test_paid") AS paid_payments,
            COALESCE(SUM(CASE WHEN payment_status = "test_paid" THEN total_price ELSE 0 END), 0) AS simulated_revenue
            FROM bookings')->fetch() ?: [];

        $reviews = $this->db->query('SELECT
            SUM(status = "pending") AS pending_reviews,
            SUM(status = "published") AS published_reviews,
            SUM(status = "rejected") AS rejected_reviews
            FROM reviews')->fetch() ?: [];

        $messages = $this->db->query('SELECT
            SUM(status = "new") AS unread_messages,
            SUM(status = "read") AS read_messages,
            SUM(status = "processed") AS processed_messages
            FROM contact_messages')->fetch() ?: [];

        return [
            'users' => (int) ($users['users'] ?? 0),
            'owners' => (int) ($users['owners'] ?? 0),
            'tenants' => (int) ($users['tenants'] ?? 0),
            'admins' => (int) ($users['admins'] ?? 0),
            'pending_users' => (int) ($users['pending_users'] ?? 0),
            'suspended_users' => (int) ($users['suspended_users'] ?? 0),
            'published_properties' => (int) ($properties['published_properties'] ?? 0),
            'pending_properties' => (int) ($properties['pending_properties'] ?? 0),
            'pending_property_changes' => (int) $this->db->query('SELECT COUNT(*) FROM property_change_requests WHERE status = "pending"')->fetchColumn(),
            'rejected_properties' => (int) ($properties['rejected_properties'] ?? 0),
            'paused_properties' => (int) ($properties['paused_properties'] ?? 0),
            'deleted_properties' => (int) ($properties['deleted_properties'] ?? 0),
            'bookings' => (int) ($bookings['bookings'] ?? 0),
            'pending_bookings' => (int) ($bookings['pending_bookings'] ?? 0),
            'pending_payment_bookings' => (int) ($bookings['pending_payment_bookings'] ?? 0),
            'confirmed_bookings' => (int) ($bookings['confirmed_bookings'] ?? 0),
            'cancelled_bookings' => (int) ($bookings['cancelled_bookings'] ?? 0),
            'completed_bookings' => (int) ($bookings['completed_bookings'] ?? 0),
            'pending_payments' => (int) ($bookings['pending_payments'] ?? 0),
            'paid_payments' => (int) ($bookings['paid_payments'] ?? 0),
            'failed_payments' => (int) $this->db->query('SELECT COUNT(*) FROM payments WHERE status = "test_failed"')->fetchColumn(),
            'pending_reviews' => (int) ($reviews['pending_reviews'] ?? 0),
            'published_reviews' => (int) ($reviews['published_reviews'] ?? 0),
            'rejected_reviews' => (int) ($reviews['rejected_reviews'] ?? 0),
            'unread_messages' => (int) ($messages['unread_messages'] ?? 0),
            'read_messages' => (int) ($messages['read_messages'] ?? 0),
            'processed_messages' => (int) ($messages['processed_messages'] ?? 0),
            'simulated_revenue' => (float) ($bookings['simulated_revenue'] ?? 0),
        ];
    }
}
