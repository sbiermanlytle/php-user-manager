//
//  LoginViewController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/27/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "RegisterProfileController.h"

@interface RegisterProfileController ()

@end

@implementation RegisterProfileController

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.title = @"Register";
    
    [self.scrollView addSubview:[self create_button:@"Submit" :@selector(submit_registration) :CGRectMake(self.sw/10, self.iy+5*(self.ih+self.ivp)+5, self.sw-self.sw/5, self.sh/12)]];
    
    self.passwordRetypeField.submit = @selector(submit_registration);
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)submit_registration
{
    if( [self validate_data] && [self validate_password] )
        [self async_task:@selector(remote_register) :@selector(catch_registration) :YES :@"Submitting..."];
}

-(void)remote_register
{
    self.httpdata = [NSData dataWithContentsOfURL:[self prepare_query:@"register"
            params:@[self.emailField.text, self.nameField.text, self.usernameField.text, self.passwordField.text]]];
}

-(void)catch_registration
{
    NSString *response = [[NSString alloc] initWithData:self.httpdata encoding:NSASCIIStringEncoding];
    if( [response isEqualToString:@"OK"] )
        [self alert:@"Success" message: @"\nAn email has been sent to your inbox with instructions on how to activate your new account."];
    else [self alert:@"Error" message: [response stringByReplacingOccurrencesOfString:@"/" withString:@"\n"] ];
}

-(LinkedTextField*) addInputField:(NSString*)title withFrame:(CGRect)frame
{
    LinkedTextField *textField = [self create_inputfield:title withFrame:frame];
    [self.scrollView addSubview:textField];
    textField.delegate = self;
    return textField;
}

#pragma mark - AlertView Delegate

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if ([alertView.title isEqualToString:@"Success"])
        [self back];
}

@end
