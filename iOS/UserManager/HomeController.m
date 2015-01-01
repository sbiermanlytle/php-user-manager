//
//  ViewController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/27/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "HomeController.h"
#import "ViewProfileController.h"
#import "RegisterProfileController.h"
#import "ForgotPasswordController.h"


@interface HomeController ()

@property(strong) UIAlertView * alert;

@end

@implementation HomeController{
    NSString *user_email;
    NSString *user_password;
}

@synthesize alert;

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    user_email = [[NSUserDefaults standardUserDefaults] stringForKey:@"email"];
    
    [self.view addSubview:[self create_button:@"Login" :@selector(nav_login) :CGRectMake(self.sw/10, self.sh/3.5, self.sw-self.sw/5, self.sh/10)]];
    [self.view addSubview:[self create_button:@"Register" :@selector(nav_register) :CGRectMake(self.sw/10, self.sh/2.2, self.sw-self.sw/5, self.sh/10)]];
    
    if ( user_email!=nil ) {
        user_password = [SSKeychain passwordForService:@"phpUM" account:user_email];
        [self async_task:@selector(remote_login) :@selector(catch_remote_login) :YES :@"Connecting..."];
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)nav_profile
{
    ViewProfileController *vc = [[ViewProfileController alloc] init];
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)nav_login
{
    [self alert_login:nil];
}

-(void)nav_forgot_password
{
    ForgotPasswordController *vc = [[ForgotPasswordController alloc] init];
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)nav_register
{
    RegisterProfileController *rvc = [[RegisterProfileController alloc] init];
    [self.navigationController pushViewController:rvc animated:YES];
}

- (void)alert_login:(NSString*)errorMessage
{
    alert = [[UIAlertView alloc] initWithTitle:@"Login" message:errorMessage delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"Login",@"Forgot Password",nil];
    
    alert.alertViewStyle = UIAlertViewStyleLoginAndPasswordInput;
    [[alert textFieldAtIndex:0] setPlaceholder:@"email"];
    
    [alert show];
}

-(void)sleeper
{
    [NSThread sleepForTimeInterval:2];
    self.debug.text = @"sleep finished";
}

-(void)remote_login
{
    if( user_email==nil || user_password==nil )
        return;
    
    self.httpdata = [NSData dataWithContentsOfURL:[self prepare_query:@"login" params:@[user_email, user_password]]];
}

-(NSString*)remote_login_query
{
    return [[NSString alloc] initWithData:self.httpdata encoding:NSUTF8StringEncoding];
}

-(void)remote_login_finish:(NSString*)response
{
    user_password = nil;
    
    [[NSUserDefaults standardUserDefaults] setObject:[response componentsSeparatedByString:@"/"] forKey:@"user_data"];
    [[NSUserDefaults standardUserDefaults] synchronize];
    
    [self nav_profile];
}

-(void)catch_remote_login
{
    NSString *response = [self remote_login_query];
    if( ![response isEqualToString:@"NO"] )
        [self remote_login_finish:response];
}

-(void)catch_remote_login_and_save
{
    NSString *response = [self remote_login_query];
    if( ![response isEqualToString:@"NO"] ){
        
        //save user data
        [[NSUserDefaults standardUserDefaults] setObject:user_email forKey:@"email"];
        
        //save user password
        [SSKeychain setPassword:user_password forService:@"phpUM" account:user_email];
        
        [self remote_login_finish:response];
    }
    else [self alert_login:@"invalid credentials"];
}

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    switch (buttonIndex) {
        case 0:
        {
            self.debug.text = @"Cancel";
            break;
        }
        case 1:
        {
            self.debug.text = @"Login";
            user_email = [alert textFieldAtIndex:0].text;
            user_password = [alert textFieldAtIndex:1].text;
            [self async_task:@selector(remote_login) :@selector(catch_remote_login_and_save) :YES :@"Connecting..."];
            break;
        }
        default:
        {
            self.debug.text = @"Forgot Password";
            [self nav_forgot_password];
            break;
        }
    }
}

@end
