/**
 * Write a description of class CustomerInformation here.
 *
 * @author (your name)
 * @version (a version number or a date)
 */

// This class represents information about a ticket purchase for a ride.
class TicketInformation {
    
    // Unique identifier for the ticket
    private int ticketId;
    
    // Name of the ride associated with the ticket
    private String rideName;
    
    // Price of the individual ticket
    private int ticketPrice;
    
    // Date when the ticket was purchased
    private String purchaseDate;
    
    // Number of tickets purchased in this transaction
    private int purchasedQuantity;

    // Constructor to initialize all fields of the ticket
    public TicketInformation(int ticketId, String rideName, int ticketPrice, String purchaseDate, int purchasedQuantity) {
        this.ticketId = ticketId;
        this.rideName = rideName;
        this.ticketPrice = ticketPrice;
        this.purchaseDate = purchaseDate;
        this.purchasedQuantity = purchasedQuantity;
    }

    // Returns the unique ID of the ticket
    public int getTicketId() {
        return ticketId;
    }

    // Returns the name of the ride associated with the ticket
    public String getRideName() {
        return rideName;
    }

    // Returns the price of the ticket
    public int getTicketPrice() {
        return ticketPrice;
    }

    // Returns the date when the ticket was purchased
    public String getPurchaseDate() {
        return purchaseDate;
    }

    // Returns the number of tickets purchased in this transaction
    public int getPurchasedQuantity() {
        return purchasedQuantity;
    }
}


