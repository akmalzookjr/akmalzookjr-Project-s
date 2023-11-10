import java.io.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.util.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Stack;
import java.util.Queue;
import java.util.LinkedList;
import java.util.Random;
import java.util.concurrent.ThreadLocalRandom;
import java.time.LocalDate;

// Import necessary packages
public class MalafanTicketingSystem extends JFrame {

    // Declare buttons, labels, and other UI components
    private JButton showDataButton, ancButton, nextQueueButton, recordButton, payButton, recButton, addNewCustomerButton;
    private JLabel countLabel;

    // Initialize count variable
    private int count = 0;

    // Declare queues, lists, and other data structures
    private Queue<CustomerInformation> counter1Queue;
    private Queue<CustomerInformation> counter2Queue;
    private Queue<CustomerInformation> counter3Queue;
    private List<CustomerInformation> customerList;
    private boolean dataLoaded = false;

    // Stack to keep track of completed transactions
    private Stack<CustomerInformation> completeStack = new Stack<>();

    // JTextAreas for displaying information
    private JTextArea textArea;
    private JTextArea customerInfoTextArea;
    private JTextArea customerInfoTextArea1;
    private JTextArea customerInfoTextArea2;
    private JTextArea customerInfoTextArea3;

    // DefaultListModel and JList for displaying services
    private DefaultListModel<String> serviceListModel = new DefaultListModel<>();
    private JList<String> serviceListDisplay;

    // Flags to track data display and changes
    private boolean dataDisplayed = false;
    private boolean dataChanged = false;

    // Flag to indicate if a new customer is added
    private boolean newCustomerAdded = true;

    // JTextArea and JComboBox for additional UI elements
    private JTextArea dataTextArea;
    private JComboBox<String> customerComboBox;

    // Variables to track processed customers at each counter
    private int customersProcessedAtCounter1 = 0;
    private int customersProcessedAtCounter2 = 0;
    private int customersProcessedAtCounter3 = 0;

    // Buttons and counters for each counter
    private JButton payButton1, recButton1;
    private JButton payButton2, recButton2;
    private JButton payButton3, recButton3;

    // Map to store paid customers based on their IDs
    private Map<Integer, List<String>> paidCustomersMap = new HashMap<>();

    // Constructor for the main class
    public MalafanTicketingSystem() {
        // Initialize queues and lists
        counter1Queue = new LinkedList<>();
        counter2Queue = new LinkedList<>();
        counter3Queue = new LinkedList<>();
        customerList = new ArrayList<>();

        // Initialize JTextAreas
        customerInfoTextArea = new JTextArea();
        customerInfoTextArea.setEditable(false);
        customerInfoTextArea.setLineWrap(true);

        // Initialize JTextAreas for each counter
        customerInfoTextArea1 = new JTextArea();
        customerInfoTextArea2 = new JTextArea();
        customerInfoTextArea3 = new JTextArea();

        // Initialize JComboBox
        customerComboBox = new JComboBox<>();

        // Set up JFrame properties
        setTitle("Malafan Ticketing System");
        setSize(1300, 650);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout(10, 10));
        getRootPane().setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));

        // Set Nimbus look and feel
        setLookAndFeel("Nimbus");

        // Define colors
        Color backgroundColor = new Color(223, 227, 230);
        Color buttonColor = new Color(100, 149, 237);

        // Create top button panel
        JPanel topButtonPanel = new JPanel();
        topButtonPanel.setLayout(new BorderLayout(5, 5));
        topButtonPanel.setBackground(backgroundColor);
        add(topButtonPanel, BorderLayout.NORTH);

        // Create button row panel
        JPanel buttonRowPanel = new JPanel();
        buttonRowPanel.setLayout(new FlowLayout(FlowLayout.CENTER, 5, 5));
        buttonRowPanel.setBackground(backgroundColor);

        // Add button row panel to the center of the top button panel
        topButtonPanel.add(buttonRowPanel, BorderLayout.CENTER);

        // Create count label and add it to the top button panel
        countLabel = new JLabel("Count: " + count);
        countLabel.setHorizontalAlignment(SwingConstants.CENTER);
        countLabel.setFont(new Font(countLabel.getFont().getName(), Font.BOLD, 18));
        topButtonPanel.add(countLabel, BorderLayout.NORTH);

        // Initialize and add buttons with their respective action listeners
        showDataButton = createStyledButton("Show Data");
        showDataButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                displayDataFromFile("customerList.txt");
            }
        });

        ancButton = createStyledButton("Add New Customer");
        ancButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                addNewCustomer();
            }
        });

        nextQueueButton = createStyledButton("Next Queue");
        nextQueueButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                // Temporary lists to hold customers based on their ticket quantity
                List<CustomerInformation> customersWithFiveOrLessTickets = new ArrayList<>();
                List<CustomerInformation> customersWithMoreThanFiveTickets = new ArrayList<>();

                // Step 1: Categorize customers based on ticket quantity
                for (CustomerInformation customer : customerList) {
                    // Assume each customer only has one TicketInformation object.
                    if (customer.getPurchasedTickets().get(0).getPurchasedQuantity() <= 5) {
                        customersWithFiveOrLessTickets.add(customer);
                    } else {
                        customersWithMoreThanFiveTickets.add(customer);
                    }
                }

                // Step 2: Add customers to the queues based on the logic provided
                // Add to Queue 1 (customers with ticket quantity <= 5)
                while (counter1Queue.size() < 5 && !customersWithFiveOrLessTickets.isEmpty()) {
                    counter1Queue.add(customersWithFiveOrLessTickets.remove(0));
                }

                // Add to Queue 2 (next set of customers with ticket quantity <= 5)
                while (counter2Queue.size() < 5 && !customersWithFiveOrLessTickets.isEmpty()) {
                    counter2Queue.add(customersWithFiveOrLessTickets.remove(0));
                }

                // Add to Queue 3 (customers with ticket quantity > 5)
                while (counter3Queue.size() < 5 && !customersWithMoreThanFiveTickets.isEmpty()) {
                    counter3Queue.add(customersWithMoreThanFiveTickets.remove(0));
                }

                // Step 3: Update the main customer list with remaining customers
                customerList.clear();
                customerList.addAll(customersWithFiveOrLessTickets);
                customerList.addAll(customersWithMoreThanFiveTickets);

                // Step 4: Write the updated customer list back to the file
                writeCustomerListToFile();

                // Step 5: Update GUI components to reflect the new state of queues and customer list
                displayQueueContents(counter1Queue, customerInfoTextArea1);
                displayQueueContents(counter2Queue, customerInfoTextArea2);
                displayQueueContents(counter3Queue, customerInfoTextArea3);

                // Update the count label with the new size of the customer list
                count = customerList.size();
                countLabel.setText("Count: " + count);
            }
        });

        // Add buttons to the buttonRowPanel
        buttonRowPanel.add(showDataButton);
        buttonRowPanel.add(ancButton);
        buttonRowPanel.add(nextQueueButton);

        // Center panels
        JPanel centerPanel = new JPanel();
        centerPanel.setLayout(new FlowLayout(FlowLayout.CENTER, 5, 5));
        centerPanel.setBackground(backgroundColor);
        add(centerPanel, BorderLayout.CENTER);

        // Create counter panels and add them to the main frame
        JPanel counterPanel1 = createCounterPanel(1, customerInfoTextArea1, buttonColor);
        JPanel counterPanel2 = createCounterPanel(2, customerInfoTextArea2, buttonColor);
        JPanel counterPanel3 = createCounterPanel(3, customerInfoTextArea3, buttonColor);

        // Add counter panels to the centerPanel
        centerPanel.add(counterPanel1);
        centerPanel.add(counterPanel2);
        centerPanel.add(counterPanel3);

        // Add buttons to the buttonRowPanel again (repeated)
        buttonRowPanel.add(showDataButton);
        buttonRowPanel.add(ancButton);
        buttonRowPanel.add(nextQueueButton);

        // Add the buttonRowPanel to the topButtonPanel so it's positioned below the countLabel
        topButtonPanel.add(buttonRowPanel);

        // Record button at the bottom
        JPanel bottomButtonPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
        bottomButtonPanel.setBackground(backgroundColor);
        add(bottomButtonPanel, BorderLayout.SOUTH);

        recordButton = createStyledButton("Record");
        recordButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                displayDataFromFile("paidCustomerList.txt");
            }
        });
        recordButton.setPreferredSize(new Dimension(800, 40));
        bottomButtonPanel.add(recordButton);

        // Add New Customer button (comment: Add it to the appropriate panel or layout as required)

        // Set font styles
        Font buttonFont = new Font("SansSerif", Font.BOLD, 14);
        applyFonts(new Component[]{
                showDataButton, ancButton, nextQueueButton, recordButton,
                payButton1, recButton1, // Assuming these are for counter 1
                payButton2, recButton2, // Assuming these are for counter 2
                payButton3, recButton3, // Assuming these are for counter 3
                addNewCustomerButton,
            }, buttonFont);

        // Window listener for closing the window
        addWindowListener(new WindowAdapter() {
            @Override
            public void windowClosing(WindowEvent e) {
                e.getWindow().dispose();
            }
        });

        // Set the frame as visible
        setVisible(true);

        // Load customer data from file on program start
        loadCustomerDataFromFile("customerList.txt");
    }

    // New function to reduce customers from the customerList.txt file
    // Modify the reduceCustomersFromFile method
    private void reduceCustomersFromFile(String fileName, int customersToReduce) {
        // Existing code to reduce customers
        List<CustomerInformation> customersToRemove = new ArrayList<>();

        // Identify customers to remove
        for (int i = 0; i < customersToReduce; i++) {
            if (!customerList.isEmpty()) {
                customersToRemove.add(customerList.remove(0));
            }
        }

        // Update the countLabel
        count = customerList.size();
        countLabel.setText("Count: " + count);

        // Write the updated customer list to the file
        writeCustomerListToFile();

        // Display a single message after reducing customers
        showMessage(customersToReduce + " customer(s) removed successfully.");

        // Track the removed customers for each queue counter
        int queueCounter1Count = 0;
        int queueCounter2Count = 0;
        int queueCounter3Count = 0;

        // Distribute removed customers among the queue counters (max 5 for each)
        for (CustomerInformation removedCustomer : customersToRemove) {
            if (queueCounter1Count < 5 && counter1Queue.size() < 5) {
                counter1Queue.add(removedCustomer);
                queueCounter1Count++;
            } else if (queueCounter2Count < 5 && counter2Queue.size() < 5) {
                counter2Queue.add(removedCustomer);
                queueCounter2Count++;
            } else if (queueCounter3Count < 5 && counter3Queue.size() < 5) {
                counter3Queue.add(removedCustomer);
                queueCounter3Count++;
            }
            // You can add additional logic for handling cases where all queue counters are full
        }
    }

    // Helper function to parse customer information from a line
    private CustomerInformation parseCustomerInformation(String line) {
        String[] parts = line.split(";");
        if (parts.length >= 7) {
            String custId = parts[0];
            String custName = parts[1];
            int ticketId = Integer.parseInt(parts[2]);
            String rideName = parts[3];
            int ticketPrice = Integer.parseInt(parts[4]);
            int purchasedQuantity = Integer.parseInt(parts[5]);
            String purchaseDate = parts[6];

            CustomerInformation customer = new CustomerInformation(custId, custName);
            TicketInformation ticket = new TicketInformation(ticketId, rideName, ticketPrice, purchaseDate, purchasedQuantity);

            for (int i = 0; i < purchasedQuantity; i++) {
                customer.addItem(ticket);
            }

            return customer;
        }
        return null;
    }

    // Helper function to write the remaining customers back to the file
    private void writeRemainingCustomersToFile(String filename, List<CustomerInformation> remainingCustomers) {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(filename, false))) {
            for (CustomerInformation customer : remainingCustomers) {
                String customerData = formatCustomerForFile(customer);
                writer.write(customerData);
                writer.newLine();
            }
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error writing customer list: " + e.getMessage());
        }
    }

    private void setLookAndFeel(String lookAndFeelName) {
        try {
            for (UIManager.LookAndFeelInfo info : UIManager.getInstalledLookAndFeels()) {
                if (lookAndFeelName.equals(info.getName())) {
                    UIManager.setLookAndFeel(info.getClassName());
                    break;
                }
            }
        } catch (Exception e) {
            try {
                UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        }
    }

    // Modify loadCustomerDataFromFile method to update count and countLabel
    private void loadCustomerDataFromFile(String filename) {
        customerList.clear(); // Clear existing data

        try (BufferedReader reader = new BufferedReader(new FileReader(filename))) {
            String line;

            while ((line = reader.readLine()) != null) {
                CustomerInformation customer = parseCustomerInformation(line);
                if (customer != null) {
                    customerList.add(customer);
                }
            }

            // Update the count and countLabel after loading the data
            count = customerList.size();
            countLabel.setText("Count: " + count);
            JOptionPane.showMessageDialog(this, "Customer data loaded successfully.");
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error loading customer data: " + e.getMessage());
        }
    }

    private void displayCustomers() {
        serviceListModel.clear();
        customerInfoTextArea.setText("");
        customerComboBox.removeAllItems();
        customerComboBox.addItem("Select a customer");

        int counter = 0;
        int counterLimit = 5;
        String currentCounter = "";
        String currentCounterMain = "";

        for (CustomerInformation customer : completeStack) {
            serviceListModel.addElement(customer.toString());
            customerInfoTextArea.append("Customer ID: " + customer.getCustId() + "\n");
            customerInfoTextArea.append("Customer Name: " + customer.getCustName() + "\n");
            customerInfoTextArea.append("Ticket ID: " + (customer.getTicketId() - 1)+ "\n");
            customerInfoTextArea.append("Counter Number: " + customer.getCounterNumber() + "\n\n");
            customerComboBox.addItem(customer.toString());

            counter++;
        }
    }

    private void displayDataFromFile(String filename) {
        String data = readDataFromFile(filename);
        if (!data.isEmpty()) {
            displayFormattedData(data);
        }
    }

    private void displayFormattedData(String data) {
        JTextArea dataTextArea = new JTextArea(data);
        dataTextArea.setEditable(false);
        dataTextArea.setLineWrap(true);
        dataTextArea.setWrapStyleWord(true);
        JScrollPane dataScrollPane = new JScrollPane(dataTextArea);
        dataScrollPane.setPreferredSize(new Dimension(400, 600));

        dataTextArea.setFont(new Font("Monospaced", Font.PLAIN, 12));
        dataTextArea.setMargin(new Insets(10, 10, 10, 10));
        dataTextArea.setOpaque(false);
        dataTextArea.setForeground(Color.BLACK);
        dataTextArea.setBackground(Color.WHITE);

        JOptionPane.showMessageDialog(this, dataScrollPane, "Customer Data", JOptionPane.INFORMATION_MESSAGE);
    }

    private String readDataFromFile(String filename) {
        try {
            BufferedReader reader = new BufferedReader(new FileReader(filename));
            StringBuilder data = new StringBuilder();
            String line;

            while ((line = reader.readLine()) != null) {
                String[] parts = line.split(";");
                if (parts.length >= 7) {
                    String custId = parts[0];
                    String custName = parts[1];
                    int ticketId = Integer.parseInt(parts[2]);
                    String rideName = parts[3];
                    int ticketPrice = Integer.parseInt(parts[4]);
                    int purchasedQuantity = Integer.parseInt(parts[5]);
                    String purchaseDate = parts[6];

                    data.append("Customer ID: ").append(custId).append("\n");
                    data.append("Customer Name: ").append(custName).append("\n");
                    data.append("Ticket ID: ").append(ticketId).append("\n");
                    data.append("Ride Name: ").append(rideName).append("\n");
                    data.append("Ticket Price: RM").append(ticketPrice).append("\n");
                    data.append("Purchased Quantity: ").append(purchasedQuantity).append("\n");
                    data.append("Purchase Date: ").append(purchaseDate).append("\n");
                    data.append("=====================================").append("\n");
                }
            }

            reader.close();
            return data.toString();
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error loading data: " + e.getMessage());
            return "";
        }
    }

    // New function to add a new customer
    private void addNewCustomer() {
        // Define the maximum limit for data entries
        int maxDataLimit = 100;

        // Read the existing data from the file and count the entries
        int existingDataCount = countExistingData("customerList.txt");

        // Calculate the number of customers needed to reach the max limit
        int customersNeeded = maxDataLimit - existingDataCount;

        // Check if the data is full
        if (existingDataCount >= maxDataLimit) {
            JOptionPane.showMessageDialog(this, "Data is full. Cannot add more customers.");
            return; // Exit the function if data is full
        }

        Random random = new Random();
        // Instead of directly writing to the file, you should add the new customers to the in-memory list first.
        List<CustomerInformation> newCustomers = new ArrayList<>();

        // Find the last customer ID and ticket ID from the customerList collection instead of reading the file each time
        int lastCustomerId = customerList.isEmpty() ? 0 : Integer.parseInt(customerList.get(customerList.size() - 1).getCustId().substring(2));
        int lastTicketId = customerList.isEmpty() ? 0 : customerList.get(customerList.size() - 1).getTicketId();

        // Generate and add new customers in a loop
        for (int i = 0; i < customersNeeded; i++) {

            try (BufferedReader reader = new BufferedReader(new FileReader("customerList.txt"))) {
                String line;
                while ((line = reader.readLine()) != null) {
                    String[] parts = line.split(";");
                    if (parts.length >= 2) {
                        int customerId = Integer.parseInt(parts[0].substring(2));
                        int ticketId = Integer.parseInt(parts[2]);
                        lastCustomerId = Math.max(lastCustomerId, customerId);
                        lastTicketId = Math.max(lastTicketId, ticketId);
                    }
                }
            } catch (IOException e) {
                e.printStackTrace();
                JOptionPane.showMessageDialog(this, "Error reading customer data: " + e.getMessage());
            }

            // Increment the last customer and ticket IDs
            int newCustomerIdNumber = lastCustomerId + 1;
            int newTicketIdNumber = lastTicketId + 1;

            // Generate new customer and ticket IDs by formatting them to your desired format
            String newCustomerId = String.format("TP%04d", newCustomerIdNumber);
            String newRidesId = String.format("%03d", newTicketIdNumber);

            // Generate a random customer name
            String[] customerNames1 = {"Muhammad", "Nurul", "Dina", "Siti", "Nor", "Aminah", "Hassan", "Fatimah", "Zain", "Zara", "Imran", "Aisyah", "Khalid", "Zainab", "Hussein", "Raihana", "Ali", "Maznah", "Salim", "Zulaikha"};
            String[] customerNames2 = {"Abdullah", "Mohd", "Ali", "Ahmad", "Hakim", "Abu", "Ibrahim", "Syafiq", "Ismail", "Mohd", "Aisyah", "Hassan", "Izzati", "Amir", "Hakeem", "Nadia", "Farah", "Dila", "Daniel", "Akmal"};
            String randomCustomerName = customerNames1[random.nextInt(customerNames1.length)] + " " + customerNames2[random.nextInt(customerNames2.length)];

            // Generate a random ride name
            String[] rideNames = {"Roller Coaster", "Ferris Wheel", "Carousel", "Haunted House", "Tea Cups", "Bumper Cars", "Water Slide", "Swing Ride", "Merry-Go-Round"};
            String randomRideName = rideNames[random.nextInt(rideNames.length)];

            // Generate a random price based on the ride name
            int randomPrice = 0;
            switch (randomRideName) {
                case "Roller Coaster":
                    randomPrice = 20;
                    break;
                case "Ferris Wheel":
                    randomPrice = 15;
                    break;
                case "Carousel":
                    randomPrice = 10;
                    break;
                case "Haunted House":
                    randomPrice = 25;
                    break;
                case "Tea Cups":
                    randomPrice = 12;
                    break;
                case "Bumper Cars":
                    randomPrice = 18;
                    break;
                case "Water Slide":
                    randomPrice = 30;
                    break;
                case "Swing Ride":
                    randomPrice = 15;
                    break;
                case "Merry-Go-Round":
                    randomPrice = 10;
                    break;
            }

            int randomQuantity = random.nextInt(10) + 1;
            String randomPurchaseDate = generateRandomDate("2024-01-21", "2025-01-01");

            String customerData = newCustomerId + ";" + randomCustomerName + ";" + newRidesId + ";" + randomRideName + ";" + randomPrice + ";" + randomQuantity + ";" + randomPurchaseDate;

            try (BufferedWriter writer = new BufferedWriter(new FileWriter("customerList.txt", true))) {
                writer.write(customerData);
                writer.newLine();
            } catch (IOException e) {
                e.printStackTrace();
                JOptionPane.showMessageDialog(this, "Error adding a new customer: " + e.getMessage());
            }
            // Create a new CustomerInformation object
            CustomerInformation newCustomer = new CustomerInformation(newCustomerId, randomCustomerName);
            TicketInformation newTicket = new TicketInformation(newTicketIdNumber, randomRideName, randomPrice, randomPurchaseDate, randomQuantity);
            newCustomer.addItem(newTicket);

            newCustomer.addItem(newTicket);

            // Add the new customer to the in-memory list
            newCustomers.add(newCustomer);

            // Update the last IDs used
            lastCustomerId = newCustomerIdNumber;
            lastTicketId = newTicketIdNumber;
        }
        // Add the newly created customers to the main customer list
        customerList.addAll(newCustomers);

        // Now write the entire customer list to the file
        writeCustomerListToFile();

        // Display a single message after adding all the generated customers
        showMessage(customersNeeded + " New customer added successfully.");
        newCustomerAdded = true;

        // Update the countLabel
        count = customerList.size(); // Assuming customerList is your in-memory list
        countLabel.setText("Count: " + count);
    }

    // Helper method to add receipt to file
    private void addReceiptToFile(String filename, String receipt) {
        // No change needed if you're just appending the formatted string to the file
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(filename, true))) {
            writer.write(receipt);
            writer.newLine();
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error writing to receipt file: " + e.getMessage());
        }
    }

    private void showMessage(String message) {
        JOptionPane.showMessageDialog(this, message);
    }

    // Helper function to count existing data entries in the file
    private int countExistingData(String filename) {
        try {
            BufferedReader reader = new BufferedReader(new FileReader(filename));
            int count = 0;

            while (reader.readLine() != null) {
                count++;
            }

            reader.close();
            return count;
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error counting existing data: " + e.getMessage());
            return 0;
        }
    }

    private void writeCustomerListToFile() {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter("customerList.txt"))) {
            for (CustomerInformation customer : customerList) {
                String customerData = formatCustomerForFile(customer);
                writer.write(customerData);
                writer.newLine();
            }
        } catch (IOException e) {
            JOptionPane.showMessageDialog(this, "Error writing customer list: " + e.getMessage());
        }
    }

    private String formatCustomerForFile(CustomerInformation customer) {
        // Get the last purchased ticket from the list of purchased tickets
        List<TicketInformation> purchasedTickets = customer.getPurchasedTickets();
        TicketInformation lastTicket = purchasedTickets.get(purchasedTickets.size() - 1);

        return String.format("%s;%s;%d;%s;%d;%d;%s",
            customer.getCustId(),
            customer.getCustName(),
            lastTicket.getTicketId(),
            lastTicket.getRideName(),
            lastTicket.getTicketPrice(),
            lastTicket.getPurchasedQuantity(),
            lastTicket.getPurchaseDate()
        );
    }

    // Helper function to generate a random date between two dates
    private String generateRandomDate(String startDate, String endDate) {
        LocalDate start = LocalDate.parse(startDate);
        LocalDate end = LocalDate.parse(endDate);
        long randomDate = ThreadLocalRandom.current().nextLong(start.toEpochDay(), end.toEpochDay());
        return LocalDate.ofEpochDay(randomDate).toString();
    }

    //To create panel for every queue counter.
    private JPanel createCounterPanel(int counterNumber, JTextArea customerInfoTextArea, Color buttonColor) {
        JPanel counterPanel = new JPanel(new BorderLayout());
        counterPanel.setBackground(new Color(223, 227, 230));

        JLabel counterLabel = new JLabel("<html><div style='text-align: center; font-size: 16px;'>Counter " + counterNumber + "</div></html>");
        counterLabel.setHorizontalAlignment(SwingConstants.CENTER);
        counterPanel.add(counterLabel, BorderLayout.NORTH);

        JButton ancButton = createStyledButton("ANC");

        JPanel buttonPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
        buttonPanel.setBackground(new Color(223, 227, 230));
        buttonPanel.add(ancButton);

        counterPanel.add(buttonPanel, BorderLayout.CENTER);

        customerInfoTextArea.setEditable(false);
        customerInfoTextArea.setLineWrap(true);
        JScrollPane customerInfoScrollPane = new JScrollPane(customerInfoTextArea);
        customerInfoScrollPane.setPreferredSize(new Dimension(250, 300));
        counterPanel.add(customerInfoScrollPane, BorderLayout.CENTER);
        payButton = createStyledButton("Payment");
        payButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent e) {
                    Queue<CustomerInformation> queue = null;
                    switch (counterNumber) {
                        case 1:
                            payButton1 = createStyledButton("Payment");
                            recButton1 = createStyledButton("Receipt");
                            queue = counter1Queue;
                            // Set any additional properties or listeners specific to counter 1
                            break;
                        case 2:
                            payButton2 = createStyledButton("Payment");
                            recButton2 = createStyledButton("Receipt");
                            queue = counter2Queue;
                            // Set any additional properties or listeners specific to counter 2
                            break;
                        case 3:
                            payButton3 = createStyledButton("Payment");
                            recButton3 = createStyledButton("Receipt");
                            queue = counter3Queue;
                            // Set any additional properties or listeners specific to counter 3
                            break;
                    }
                    if (queue != null && !queue.isEmpty()) {
                        CustomerInformation customer = queue.poll(); // Remove the first customer from the queue
                        String receipt = generateReceipt(customer); // Generate the receipt for the customer
                        addReceiptToFile("paidCustomerList.txt", receipt); // Add the receipt to paidCustomerList.txt
                        addToTemporaryMemory(counterNumber, receipt); // Add the receipt to temporary memory
                        displayQueueContents(queue, customerInfoTextArea); // Update the display
                    } else {
                        JOptionPane.showMessageDialog(MalafanTicketingSystem.this, "No customers in queue.");
                    }
                    // Now apply the bold font to these buttons specifically
                    Font buttonFont = new Font("SansSerif", Font.BOLD, 14);
                    applyFonts(new Component[]{payButton1, recButton1, payButton2, recButton2, payButton3, recButton3}, buttonFont);
                }
            });

        // Inside the createCounterPanel method, modify the recButton's ActionListener
        recButton = createStyledButton("Receipt");
        recButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent e) {
                    List<String> receipts = paidCustomersMap.getOrDefault(counterNumber, new ArrayList<>());
                    displayReceipts(receipts, counterNumber); // Pass the counter number if needed for unique formatting
                }
            });

        JPanel bottomButtonPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
        bottomButtonPanel.setBackground(new Color(223, 227, 230));
        bottomButtonPanel.add(payButton);
        bottomButtonPanel.add(recButton);

        counterPanel.add(bottomButtonPanel, BorderLayout.SOUTH);

        // Display the content of the respective counter queue in the JTextArea
        switch (counterNumber) {
            case 1:
                displayQueueContents(counter1Queue, customerInfoTextArea);
                break;
            case 2:
                displayQueueContents(counter2Queue, customerInfoTextArea);
                break;
            case 3:
                displayQueueContents(counter3Queue, customerInfoTextArea);
                break;
        }

        return counterPanel;
    }

    // Add the new displayReceipts method
    private void displayReceipts(List<String> receipts, int counterNumber) {
        // Create a JTextArea to display the receipts
        JTextArea receiptTextArea = new JTextArea(20, 40);
        receiptTextArea.setEditable(false);
        receiptTextArea.setMargin(new Insets(5, 5, 5, 5));
        receiptTextArea.setFont(new Font("Segoe UI", Font.PLAIN, 12));

        // Format each receipt and append it to the JTextArea
        for (String receipt : receipts) {
            // Split the receipt data to separate it out from the tokenizer format
            String[] receiptData = receipt.split(";");
            // Check if the receiptData array has the expected number of elements
            if (receiptData.length == 7) {
                receiptTextArea.append("Customer ID: " + receiptData[0] + "\n");
                receiptTextArea.append("Customer Name: " + receiptData[1] + "\n");
                receiptTextArea.append("Ticket ID: " + receiptData[2] + "\n");
                receiptTextArea.append("Ride Name: " + receiptData[3] + "\n");
                receiptTextArea.append("Ticket Price: " + receiptData[4] + "\n");
                receiptTextArea.append("Quantity Purchased: " + receiptData[5] + "\n");
                receiptTextArea.append("Purchase Date: " + receiptData[6] + "\n");
                receiptTextArea.append("------------------------------------------\n");
            }
        }

        // Wrap the JTextArea in a JScrollPane
        JScrollPane scrollPane = new JScrollPane(receiptTextArea);
        scrollPane.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_ALWAYS);

        // Show the dialog
        JOptionPane.showMessageDialog(null, scrollPane, "Receipts for Counter " + counterNumber, JOptionPane.INFORMATION_MESSAGE);
    }

    // Helper method to add receipt to temporary memory
    private void addToTemporaryMemory(int counterNumber, String receipt) {
        List<String> receipts = paidCustomersMap.getOrDefault(counterNumber, new ArrayList<>());
        receipts.add(receipt);
        paidCustomersMap.put(counterNumber, receipts);
    }

    // Helper method to generate a receipt for a customer
    private String generateReceipt(CustomerInformation customer) {
        // Assuming customer has a method to get all purchased tickets
        List<TicketInformation> tickets = customer.getPurchasedTickets();
        // Retrieve the last purchased ticket
        TicketInformation lastTicket = tickets.get(tickets.size() - 1);

        // Format the receipt as per the requirement
        return String.format("%s;%s;%d;%s;%d;%d;%s",
            customer.getCustId(),
            customer.getCustName(),
            lastTicket.getTicketId(),
            lastTicket.getRideName(),
            lastTicket.getTicketPrice(),
            lastTicket.getPurchasedQuantity(),
            lastTicket.getPurchaseDate()
        );
    }

    // Helper method to display receipts
    private void displayReceipts(List<String> receipts) {
        StringBuilder allReceipts = new StringBuilder();
        for (String receipt : receipts) {
            allReceipts.append(receipt).append("\n");
        }
        // You might want to use a JTextArea inside a JScrollPane if the receipts are many
        JOptionPane.showMessageDialog(this, allReceipts.toString(), "Receipts", JOptionPane.INFORMATION_MESSAGE);
    }

    private void displayQueueContents(Queue<CustomerInformation> queue, JTextArea textArea) {
        textArea.setText("");
        textArea.append("Queue Contents:\n");
        textArea.append("======================================================\n");

        int customerNumber = 1;
        for (CustomerInformation customer : queue) {
            textArea.append(customerNumber + "." + "\t" + "Customer ID: " + customer.getCustId() + "\n");
            textArea.append("\t" + "Customer Name: " + customer.getCustName() + "\n");
            textArea.append("\t" + "Ticket Quantity: " + customer.getPurchasedTickets().get(0).getPurchasedQuantity() + "\n");
            textArea.append("======================================================\n");

            customerNumber++;
        }
    }

    private JButton createStyledButton(String text) {
        JButton button = new JButton(text);
        button.setPreferredSize(new Dimension(200, 40));
        button.setBackground(new Color(176, 224, 230));
        button.setForeground(Color.BLACK);
        button.setBorder(BorderFactory.createLineBorder(new Color(135, 206, 235), 2));
        return button;
    }

    private void applyFonts(Component[] components, Font font) {
        for (Component component : components) {
            if (component != null) {
                component.setFont(font);
            }
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
                    new MalafanTicketingSystem();
            });
    }
}
