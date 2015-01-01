//
//  EditProfileController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/31/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "EditProfileController.h"

@implementation EditProfileController
{
    NSArray *user_data;
}

@synthesize passwordOldField;

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.title = @"Edit Profile";
    
    [self.scrollView addSubview:[self create_button:@"Save Info" :@selector(submit_edit) :CGRectMake(self.sw/10, self.iy+3*(self.ih+self.ivp)+5, self.sw-self.sw/5, self.sh/12)]];
    
    [self.scrollView addSubview:[self create_button:@"Change Password" :@selector(submit_password_change) :CGRectMake(self.sw/10, self.iy+7*(self.ih+self.ivp)+25, self.sw-self.sw/5, self.sh/12)]];
    
    self.emailField.text = [[NSUserDefaults standardUserDefaults] stringForKey:@"email"];
    user_data = (NSArray*)[[NSUserDefaults standardUserDefaults] objectForKey:@"user_data"];
    self.usernameField.text = [user_data objectAtIndex:2];
    self.nameField.text = [user_data objectAtIndex:3];
    
    passwordOldField = [self addInputField:@"Old Password" withFrame:CGRectMake(self.ip, self.iy+4*(self.ih+self.ivp)+20, self.sw-self.ip*2, self.ih)];
    
    passwordOldField.secureTextEntry = YES;
    
    [self.passwordField setFrame:CGRectMake(self.ip, self.iy+5*(self.ih+self.ivp)+20, self.sw-self.ip*2, self.ih)];
    [self.passwordRetypeField setFrame:CGRectMake(self.ip, self.iy+6*(self.ih+self.ivp)+20, self.sw-self.ip*2, self.ih)];
    
    self.usernameField.nextField = nil;
    passwordOldField.nextField = self.passwordField;
    self.passwordRetypeField.submit = @selector(submit_password_change);
    self.usernameField.submit = @selector(submit_edit);
    
    self.scrollView.contentSize = CGSizeMake(self.sw,self.sh*1.1);
}

-(void)submit_edit
{
    if( [self validate_data] )
        [self async_task:@selector(remote_edit) :@selector(catch_edit) :YES :@"Saving..."];
}

-(void)submit_password_change
{
    if( [self.passwordOldField.text isEqualToString:@""] ){
        [self alert:@"Error" message:@"\nYou must provide your old password in order to set a new password."];
        return;
    } else if( [self validate_password] )
        [self async_task:@selector(remote_password_change) :@selector(catch_password_change) :YES :@"Saving..."];
}

-(void)remote_edit
{
    self.httpdata = [NSData dataWithContentsOfURL:[self prepare_query:@"edit"
        params:@[self.emailField.text, self.usernameField.text, self.nameField.text]]];
}

-(void)remote_password_change
{
    self.httpdata = [NSData dataWithContentsOfURL:[self prepare_query:@"pc"
        params:@[self.passwordOldField.text, self.passwordField.text, self.passwordRetypeField.text]]];
}

-(void)catch_edit
{
    NSString *response = [[NSString alloc] initWithData:self.httpdata encoding:NSASCIIStringEncoding];
    if( [response isEqualToString:@"OK"] ){
        [self alert:@"Success" message: @"\nYour profile has been updated."];
        
        [[NSUserDefaults standardUserDefaults] setObject:self.emailField.text forKey:@"email"];
        NSArray *new_data =@[[user_data objectAtIndex:0],[user_data objectAtIndex:1],
                             self.usernameField.text, self.nameField.text, [user_data objectAtIndex:4]];
        [[NSUserDefaults standardUserDefaults] setObject:new_data forKey:@"user_data"];
        [[NSUserDefaults standardUserDefaults] synchronize];
    }
    else [self alert:@"Error" message: [response stringByReplacingOccurrencesOfString:@"/" withString:@"\n"] ];
}

-(void)catch_password_change
{
    NSString *response = [[NSString alloc] initWithData:self.httpdata encoding:NSASCIIStringEncoding];
    if( [response isEqualToString:@"OK"] ){
        [self alert:@"Success" message: @"\nYour password has been changed."];
        
        if([self.passwordField.text isEqualToString:@""])
            [SSKeychain setPassword:self.passwordField.text forService:@"phpUM" account:self.emailField.text];
    }
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