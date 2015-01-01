//
//  ProfileController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/31/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "ProfileController.h"

@implementation ProfileController

@synthesize scrollView;
@synthesize usernameField;
@synthesize emailField;
@synthesize nameField;
@synthesize passwordField;
@synthesize passwordRetypeField;

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    scrollView = [self create_scrollView];
    scrollView.delegate = self;
    
    _iy = 20;
    _ip = 40;
    _ih = self.sh/12;
    _ivp = 5;
    
    emailField = [self addInputField:@"Email" withFrame:CGRectMake(_ip, _iy, self.sw-_ip*2, _ih)];
    nameField = [self addInputField:@"Name" withFrame:CGRectMake(_ip, _iy+_ih+_ivp, self.sw-_ip*2, _ih)];
    usernameField = [self addInputField:@"Username" withFrame:CGRectMake(_ip, _iy+2*(_ih+_ivp), self.sw-_ip*2, _ih)];
    passwordField = [self addInputField:@"Password" withFrame:CGRectMake(_ip, _iy+3*(_ih+_ivp), self.sw-_ip*2, _ih)];
    passwordRetypeField = [self addInputField:@"Retype Password" withFrame:CGRectMake(_ip, _iy+4*(_ih+_ivp), self.sw-_ip*2, _ih)];
    
    passwordField.secureTextEntry = YES;
    passwordRetypeField.secureTextEntry = YES;
    passwordRetypeField.returnKeyType = UIReturnKeyDone;
    
    emailField.nextField = nameField;
    nameField.nextField = usernameField;
    usernameField.nextField = passwordField;
    passwordField.nextField = passwordRetypeField;
    
    self.debug.text = nil;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(BOOL)validate_data
{
    NSString *msg = @"";
    if([emailField.text isEqualToString:@""])
        msg = [msg stringByAppendingString:@"\nEmail address required"];
    if([nameField.text isEqualToString:@""])
        msg = [msg stringByAppendingString:@"\nName required"];
    if([usernameField.text isEqualToString:@""])
        msg = [msg stringByAppendingString:@"\nUsername required"];
    
    if( [msg isEqualToString:@""] )
        return YES;
    
    [self alert:@"Error" message:msg];
    return NO;
}

-(BOOL)validate_password
{
    NSString *msg = @"";
    if([passwordField.text isEqualToString:@""])
        msg = [msg stringByAppendingString:@"\nPassword required"];
    if([passwordRetypeField.text isEqualToString:@""])
        msg = [msg stringByAppendingString:@"\nPassword re-type required"];
    
    if( [msg isEqualToString:@""] && ![passwordField.text isEqualToString:passwordRetypeField.text] ){
        msg = @"\nPasswords do not match";
        passwordField.text = @"";
        passwordRetypeField.text = @"";
    }
    if( [msg isEqualToString:@""] )
        return YES;
    
    [self alert:@"Error" message:msg];
    return NO;
}

-(LinkedTextField*) addInputField:(NSString*)title withFrame:(CGRect)frame
{
    LinkedTextField *textField = [self create_inputfield:title withFrame:frame];
    [scrollView addSubview:textField];
    textField.delegate = self;
    return textField;
}

#pragma mark - TextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    
    if ([textField isKindOfClass:[LinkedTextField class]]){
        LinkedTextField *next = [(LinkedTextField *)textField nextField];
        if (next) {
            [next becomeFirstResponder];
            return NO;
        } else {
            [textField resignFirstResponder];
            [(LinkedTextField *)textField submit];
        }
    }
    return YES;
}

@end
