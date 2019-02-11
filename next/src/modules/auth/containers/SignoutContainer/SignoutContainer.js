import React from 'react'
import PropTypes from 'prop-types'
import { Mutation } from 'react-apollo'
import Router from 'next/router'

import { logoutMutation } from './mutations'

/**
 * Submit handler: switch between register and login based on form values.
 */
// const handleSubmit = ({ logout }) => variables =>
//   logout()
//     .then(redirect)
//     .catch(normalizeError)

const SignoutContainer = ({ children, user }) => (
  <Mutation mutation={ logoutMutation } variables={ { 'uid': user.uid } } onCompleted={ () => Router.push('/index', '/') }>
    {(userLogout) => children(userLogout)}
  </Mutation>
)

SignoutContainer.propTypes = {
  children: PropTypes.func,
  user: PropTypes.object,
}

export default SignoutContainer
