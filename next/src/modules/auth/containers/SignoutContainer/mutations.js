import gql from 'graphql-tag'

export const logoutMutation = gql`
  mutation UserLogout($uid: Int!) {
    userLogout(uid: $uid)
  }
`
