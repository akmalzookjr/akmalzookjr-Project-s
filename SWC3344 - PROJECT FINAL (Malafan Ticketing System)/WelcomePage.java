/**
 * Write a description of class frotn here.
 *
 * @author (your name)
 * @version (a version number or a date)
 */

import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
public class WelcomePage extends JFrame implements ActionListener {
    JButton startButton;
    public WelcomePage() 
    {

        // Create a title for Theme Park
        setTitle("Welcome to CELESTIAL MANAFAN FANTASY");
        setSize(700, 700);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        ImageIcon icon = new ImageIcon("icons.jpg");
        // Set the icon image for the frame
        setIconImage(icon.getImage());
        // Create image icon logo
        ImageIcon logo = null;
        try {
            logo = new ImageIcon("logo1.jpg");
        } catch (Exception e) {
            e.printStackTrace();
        }
        JLabel logoLabel = new JLabel(logo);
        // Create a Panel
        JPanel panel = new JPanel();
        panel.setLayout(new BorderLayout());
        add(panel);

        // Create Start Button
        startButton = new JButton("Start");
        startButton.addActionListener(this);
        panel.add(startButton, BorderLayout.SOUTH);
        startButton.setBackground(new Color(176, 224, 230));
        startButton.setBounds(200, 250, 100, 40);
        panel.add(logoLabel);
        setVisible(true);
    }

    public void actionPerformed(ActionEvent e) {
        if (e.getSource() == startButton) {
            dispose(); // Close the WelcomePage window
            new MalafanTicketingSystem().setVisible(true); // Create and show the ThemeParkTicketingSystem window
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(new Runnable() {
                public void run() {
                    new WelcomePage();
                }
            });
    }
}