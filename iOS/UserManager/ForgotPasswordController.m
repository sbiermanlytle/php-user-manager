//
//  ForgotPasswordViewController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/31/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "ForgotPasswordController.h"

@interface ForgotPasswordController ()

@end

@implementation ForgotPasswordController
{
    LinkedTextField *emailField;
    UILabel *key;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    self.title = @"Forgot Password";
    
    [self.view addSubview:[self create_uilabel:@"Reset Key:" withFrame:CGRectMake(self.sw/10, self.sh/3-50, self.sw-self.sw/5, 60)]];
    
    key = [self create_uilabel:[self randomString:43] withFrame:CGRectMake(self.sw/10, self.sh/3, self.sw-self.sw/5, 60)];
    [self.view addSubview:key];
    
    emailField = [self create_inputfield:@"Email" withFrame:CGRectMake(self.sw/10, self.sh/2.2, self.sw-self.sw/5, self.sh/10)];
    emailField.delegate = self;
    emailField.returnKeyType = UIReturnKeyDone;
    [self.view addSubview:emailField];
    
    [self.view addSubview:[self create_button:@"Copy Key & Email Reset Link" :@selector(copy_key) :CGRectMake(self.sw/10, self.sh/2.2+self.sh/10+20, self.sw-self.sw/5, self.sh/10)]];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)copy_key
{
    if ([emailField.text isEqualToString:@""])
        [self alert:@"Error" message:@"\nProvide your email"];
    else [self async_task:@selector(remote_password_change_request) :@selector(catch_password_change_request) :YES :@"Submitting..."];
}

-(void)remote_password_change_request
{
    self.httpdata = [NSData dataWithContentsOfURL:[self prepare_query:@"fp"
        params:@[emailField.text, key.text]]];
}

-(void)catch_password_change_request
{
    NSString *response = [[NSString alloc] initWithData:self.httpdata encoding:NSASCIIStringEncoding];
    if ([response isEqualToString:@"OK"]){
        UIPasteboard *pb = [UIPasteboard generalPasteboard];
        [pb setString:key.text];
        [self alert:@"Success" message:@"\nAn email has been sent to your inbox with instructions on how to reset your password using the key that has been copied to your clipboard. The key will expire in 5 minutes."];
    } else [self alert:@"Error" message:response];
}

// Generates alpha-numeric-random string
- (NSString *)randomString:(int)len {
    static NSString *letters = @"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    NSMutableString *randomString = [NSMutableString stringWithCapacity: len];
    for (int i=0; i<len; i++) {
        [randomString appendFormat: @"%C", [letters characterAtIndex: arc4random() % [letters length]]];
    }
    return randomString;
}

#pragma mark - AlertView Delegate

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if ([alertView.title isEqualToString:@"Success"])
        [self back];
}

#pragma mark - TextField Delegate

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    
    [textField resignFirstResponder];
    [self copy_key];
    return YES;
}

@end
