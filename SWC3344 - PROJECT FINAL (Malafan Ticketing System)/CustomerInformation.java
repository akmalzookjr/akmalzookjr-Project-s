
/**
 * Write a description of class CustomerInformation here.
 *
 * @author (your name)
 * @version (a version number or a date)
 */

import java.util.*;

// This class represents customer information including their purchased tickets
class CustomerInformation {

    // Unique customer identifier
    private String custId;
    
    // Name of the customer
    private String custName;
    
    // Represents the latest ticket ID purchased by this customer
    private int ticketId;
    
    // Represents the counter number where the customer makes purchases
    private int counterNumber;
    
    // List of tickets purchased by this customer
    private List<TicketInformation> purchasedTickets;

    // Constructor to initialize customer information with ID and name
    public CustomerInformation(String custId, String custName) {
        this.custId = custId;
        this.custName = custName;
        this.ticketId = 0; // Setting initial ticketId to 0
        this.purchasedTickets = new ArrayList<>();
    }

    // Adds a ticket to the list of tickets purchased by this customer
    public void addItem(TicketInformation ticket) {
        purchasedTickets.add(ticket);
    }

    // Returns the customer ID
    public String getCustId() {
        return custId;
    }

    // Returns the customer name
    public String getCustName() {
        return custName;
    }

    // Returns the latest ticket ID purchased by the customer
    public int getTicketId() {
        return ticketId;
    }

    // Increments the ticket ID when a new ticket is purchased
    public void incrementCounterPaid() {
        ticketId++;
    }

    // Returns the list of tickets purchased by the customer
    public List<TicketInformation> getPurchasedTickets() {
        return purchasedTickets;
    }

    // Returns the counter number associated with the customer
    public int getCounterNumber() {
        return counterNumber;
    }

    // Sets the counter number for the customer
    public void setCounterNumber(int counterNumber) {
        this.counterNumber = counterNumber;
    }

    // Returns a string representation of the customer, displaying their ID and name
    @Override
    public String toString() {
        return custId + " - " + custName;
    }
}



